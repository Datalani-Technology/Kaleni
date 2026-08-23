<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasEnabledTwoFactor()) {
            return redirect()->route('admin.2fa.manage');
        }

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = (new Google2FA())->generateSecretKey();
            $user->save();
        }

        $otpauthUrl = (new Google2FA())->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('admin.security.setup', [
            'qrSvg' => $this->renderQr($otpauthUrl),
            'secret' => $user->two_factor_secret,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();

        if (!$user->two_factor_secret) {
            return redirect()->route('admin.2fa.setup')->withErrors(['code' => 'Start setup again.']);
        }

        if (!(new Google2FA())->verifyKey($user->two_factor_secret, trim($request->code))) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }

        $codes = $this->generateRecoveryCodes();

        $user->two_factor_confirmed_at = now();
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        AuditLogger::record('2fa.enabled');

        $request->session()->put('admin_2fa_recovery_codes', $codes);

        return redirect()->route('admin.2fa.recovery-codes');
    }

    public function showRecoveryCodes(Request $request): View
    {
        $codes = $request->session()->pull('admin_2fa_recovery_codes');

        return view('admin.security.recovery-codes', ['codes' => $codes]);
    }

    public function manage(Request $request): View
    {
        return view('admin.security.manage', ['user' => $request->user()]);
    }

    public function regenerateCodes(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        $codes = $this->generateRecoveryCodes();
        $request->user()->forceFill(['two_factor_recovery_codes' => $codes])->save();

        AuditLogger::record('2fa.recovery_codes_regenerated');
        $request->session()->put('admin_2fa_recovery_codes', $codes);

        return redirect()->route('admin.2fa.recovery-codes');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        AuditLogger::record('2fa.reset');

        return redirect()->route('admin.2fa.setup')
            ->with('status', 'Two-factor authentication reset. Set it up again to continue using the admin panel.');
    }

    private function generateRecoveryCodes(): array
    {
        return collect(range(1, 8))->map(fn () => Str::random(5) . '-' . Str::random(5))->all();
    }

    private function renderQr(string $otpauthUrl): string
    {
        $renderer = new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd());
        $svg = (new Writer($renderer))->writeString($otpauthUrl);

        return preg_replace('/^<\?xml.*?\?>/', '', $svg) ?? $svg;
    }
}
