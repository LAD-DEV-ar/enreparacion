<?php

namespace App\Http\Controllers\Negocios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NegocioController extends Controller
{
    public function index(){
        return view('negocios.registro-negocios');
    }
}
