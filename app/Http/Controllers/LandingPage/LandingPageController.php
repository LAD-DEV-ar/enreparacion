<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('home.landing_page');
    }
}
