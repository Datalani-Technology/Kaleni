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
        $logoText = Setting::get('logo_text', '/Namsa Florals');

        return view('admin.logo.edit', compact('logoPath', 'logoText'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:6144',
            'logo_text' => 'nullable|string|max:100',
            'remove_logo' => 'nullable|boolean',
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
            Setting::set('logo_text', $request->input('logo_text') ?: '/Namsa Florals');
        }

        return redirect()->route('admin.logo.edit')
            ->with('success', 'Logo updated successfully.');
    }
}
