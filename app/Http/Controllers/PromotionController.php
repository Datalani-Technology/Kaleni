<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    /**
     * These are the two menu categories that are, in effect, Kaleni's
     * ready-made "packages" — there's no separate Package model, so this
     * page pulls the real menu items rather than showing invented pricing.
     */
    private const PACKAGE_CATEGORIES = ['Lunch & Dinner Packs', 'Sharing Platters'];

    public function index()
    {
        $pdfPath = Setting::get('promotion_pdf_path');

        if ($pdfPath && !Storage::disk('public')->exists($pdfPath)) {
            $pdfPath = null;
        }

        $packages = MenuItem::where('is_active', true)
            ->whereIn('category', self::PACKAGE_CATEGORIES)
            ->orderBy('category')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('promotion', compact('pdfPath', 'packages'));
    }
}
