<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function edit()
    {
        $logoPath = Setting::get('logo_path');
        $logoText = Setting::get('logo_text', 'Kaleni Catering Services');
        $invoicePaymentAccount = Setting::get('invoice_payment_account', '');

        return view('admin.logo.edit', compact('logoPath', 'logoText', 'invoicePaymentAccount'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'logo_text' => 'nullable|string|max:100',
            'remove_logo' => 'nullable|boolean',
            'invoice_payment_account' => 'nullable|string|max:1000',
        ]);

        if ($request->boolean('remove_logo')) {
            $old = Setting::get('logo_path');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('logo_path', '');
        } elseif ($request->hasFile('logo')) {
            $old = Setting::get('logo_path');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo_path', $path);
        }

        if ($request->has('logo_text')) {
            Setting::set('logo_text', $request->input('logo_text') ?: 'Kaleni Catering Services');
        }

        if ($request->has('invoice_payment_account')) {
            Setting::set('invoice_payment_account', $request->input('invoice_payment_account', ''));
        }

        return redirect()->route('admin.logo.edit')
            ->with('success', 'Logo updated successfully.');
    }
}
