<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminPasswordResetMail;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const RESET_THROTTLE = 3;
    private const RESET_DECAY = 600; // 10 min
    private const TOKEN_EXPIRE_MIN = 60;
    private const MAX_FAILED_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function showLogin()
    {
        if (Auth::check() && Auth::user() && in_array(Auth::user()->role, ['admin', 'editor'], true)) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $key = 'admin-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withErrors(['email' => 'Too many login attempts. Try again in ' . ceil($seconds / 60) . ' minutes.'])
                ->withInput();
        }

        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $minutes = max(1, now()->diffInMinutes($user->locked_until, false) + 1);
            RateLimiter::hit($key, 900);
            return back()
                ->withErrors(['email' => "Account temporarily locked. Try again in {$minutes} minute(s)."])
                ->withInput();
        }

        $isEligible = $user && in_array($user->role, ['admin', 'editor'], true);

        if ($isEligible && Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            RateLimiter::clear($key);
            $user->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $user->establishAdminSession($request);
            AuditLogger::record('login.success', ['email' => $user->email]);
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($isEligible) {
            $user->failed_login_attempts++;
            if ($user->failed_login_attempts >= self::MAX_FAILED_ATTEMPTS) {
                $user->locked_until = now()->addMinutes(self::LOCKOUT_MINUTES);
                $user->failed_login_attempts = 0;
            }
            $user->save();
        }

        RateLimiter::hit($key, 900);
        AuditLogger::record('login.failed', ['email' => $request->email]);
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }

    public function logout(Request $request)
    {
        AuditLogger::record('logout');
        if ($user = Auth::user()) {
            $user->forceFill(['current_session_id' => null])->save();
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $key = 'admin-forgot:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, self::RESET_THROTTLE)) {
            return back()->withErrors(['email' => 'Too many requests. Please try again later.']);
        }

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->whereIn('role', ['admin', 'editor'])->first();
        if (!$user) {
            RateLimiter::hit($key, self::RESET_DECAY);
            return back()->with('status', 'If that email exists, we sent a reset link.');
        }

        $token = Str::random(64);
        $hashed = Hash::make($token);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashed, 'created_at' => now()]
        );

        try {
            Mail::to($user->email)->send(new AdminPasswordResetMail($user->email, $token));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Admin password reset email failed', ['email' => $user->email, 'error' => $e->getMessage()]);
            return back()->withErrors(['email' => 'Could not send email. Try again later.']);
        }

        RateLimiter::hit($key, self::RESET_DECAY);
        AuditLogger::record('password.reset_requested', ['email' => $user->email]);
        return redirect()->route('admin.login')->with('status', 'If that email exists, we sent a password reset link.');
    }

    public function showResetPassword(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');
        if (!$email || !$token) {
            return redirect()->route('admin.login')->with('error', 'Invalid reset link.');
        }
        return view('admin.reset-password', ['email' => $email, 'token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $row = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$row || !Hash::check($request->token, $row->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset link.'])->withInput();
        }

        $created = $row->created_at ? \Carbon\Carbon::parse($row->created_at) : null;
        if ($created && $created->addMinutes(self::TOKEN_EXPIRE_MIN)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('admin.login')->with('error', 'Reset link expired. Request a new one.');
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->failed_login_attempts = 0;
        $user->locked_until = null;
        $user->save();
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        AuditLogger::record('password.reset', ['email' => $user->email]);

        return redirect()->route('admin.login')->with('status', 'Password updated. You can log in now.');
    }
}
