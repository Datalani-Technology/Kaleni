<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function index()
    {
        return view('terms');
    }

    public function delivery()
    {
        return view('delivery');
    }

    public function cancellations()
    {
        return view('cancellations');
    }
}
