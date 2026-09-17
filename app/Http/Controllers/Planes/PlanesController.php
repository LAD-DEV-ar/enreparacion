<?php

namespace App\Http\Controllers\Planes;

use App\Http\Controllers\Controller;

class PlanesController extends Controller
{
    public function index()
    {
        return view('home.planes');
    }
}
