<?php

namespace App\Http\Middleware;

use App\Support\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Two related protections for authenticated admin/editor requests:
 *
 *  - Idle timeout: force logout after N minutes with no admin activity,
 *    independent of the overall session lifetime (config('session.lifetime')).
 *  - Single active session: a fresh login (see AuthController/TwoFactorChallengeController)
 *    stamps a random token on both the user record and the session. Any other
 *    session for that account — including one silently restored by a
 *    "remember me" cookie — won't carry that token and gets logged out, so at
 *    most one session per admin account is ever considered valid.
 */
class AdminSessionSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        $idleMinutes = (int) config('admin.idle_timeout_minutes', 20);
        $lastActivity = $request->session()->get('admin_last_activity');

        if ($idleMinutes > 0 && $lastActivity && (time() - $lastActivity) > ($idleMinutes * 60)) {
            return $this->forceLogout($request, $user->email, 'Your session timed out due to inactivity. Please log in again.');
        }

        $sessionToken = $request->session()->get('admin_session_token');
        if (!$sessionToken || !hash_equals((string) $user->current_session_id, (string) $sessionToken)) {
            return $this->forceLogout($request, $user->email, 'You were logged out because your account signed in elsewhere.');
        }

        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }

    private function forceLogout(Request $request, ?string $email, string $message): Response
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        AuditLogger::record('session.forced_logout', ['email' => $email, 'reason' => $message]);

        return redirect()->route('admin.login')->with('error', $message);
    }
}
