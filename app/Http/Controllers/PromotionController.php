<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class PromotionController extends Controller
{
    public function index()
    {
        $pdfPath = Setting::get('promotion_pdf_path');

        return view('promotion', compact('pdfPath'));
    }
}
