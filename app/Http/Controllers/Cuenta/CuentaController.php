<?php

namespace App\Http\Controllers\Cuenta;

use App\Http\Controllers\Controller;

class CuentaController extends Controller
{
    public function index()
    {
        return view('home.cuenta');
    }
}
