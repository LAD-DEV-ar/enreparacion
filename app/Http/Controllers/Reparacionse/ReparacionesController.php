<?php

namespace App\Http\Controllers\Reparacionse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReparacionesController extends Controller
{
    public function index(){
        return view('home.reparaciones');
    }
}
