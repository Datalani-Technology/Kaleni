<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromotionCatalogController extends Controller
{
    public function edit()
    {
        $pdfPath = Setting::get('promotion_pdf_path');

        return view('admin.promotion.edit', compact('pdfPath'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'catalog' => 'nullable|file|mimes:pdf|max:20480',
            'remove_catalog' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_catalog')) {
            $old = Setting::get('promotion_pdf_path');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('promotion_pdf_path', '');
        } elseif ($request->hasFile('catalog')) {
            $old = Setting::get('promotion_pdf_path');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('catalog')->store('promotion', 'public');
            Setting::set('promotion_pdf_path', $path);
        }

        return redirect()->route('admin.promotion.edit')
            ->with('success', 'Promotion catalog updated successfully.');
    }
}
