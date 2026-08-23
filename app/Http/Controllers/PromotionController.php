<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function index()
    {
        $pdfPath = Setting::get('promotion_pdf_path');

        if ($pdfPath && !Storage::disk('public')->exists($pdfPath)) {
            $pdfPath = null;
        }

        return view('promotion', compact('pdfPath'));
    }
}
