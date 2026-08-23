<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (!$this->pendingLogin($request)) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please log in again.');
        }

        return view('admin.login-verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        $pending = $this->pendingLogin($request);
        if (!$pending) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please log in again.');
        }

        $request->validate(['code' => 'required|string']);

        $key = 'admin-2fa:' . $pending['user_id'];
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => 'Too many attempts. Try again in ' . ceil($seconds / 60) . ' minute(s).']);
        }

        $user = User::find($pending['user_id']);
        if (!$user) {
            $request->session()->forget('admin_2fa_pending');
            return redirect()->route('admin.login');
        }

        $code = trim($request->code);
        $usedRecovery = false;
        $ok = (new Google2FA())->verifyKey($user->two_factor_secret, $code);

        if (!$ok && $user->two_factor_recovery_codes) {
            $codes = $user->two_factor_recovery_codes;
            if (in_array($code, $codes, true)) {
                $ok = true;
                $usedRecovery = true;
                $user->two_factor_recovery_codes = array_values(array_diff($codes, [$code]));
                $user->save();
            }
        }

        if (!$ok) {
            RateLimiter::hit($key, 600);
            AuditLogger::record('2fa.failed', ['email' => $user->email]);
            return back()->withErrors(['code' => 'Invalid code.']);
        }

        RateLimiter::clear($key);
        $request->session()->forget('admin_2fa_pending');

        Auth::login($user, $pending['remember']);
        $request->session()->regenerate();
        $user->establishAdminSession($request);

        AuditLogger::record($usedRecovery ? '2fa.recovery_code_used' : '2fa.success', ['email' => $user->email]);

        return redirect()->intended(route('admin.dashboard'));
    }

    private function pendingLogin(Request $request): ?array
    {
        $pending = $request->session()->get('admin_2fa_pending');

        if (!$pending || ($pending['expires_at'] ?? 0) < now()->timestamp) {
            $request->session()->forget('admin_2fa_pending');
            return null;
        }

        return $pending;
    }
}
