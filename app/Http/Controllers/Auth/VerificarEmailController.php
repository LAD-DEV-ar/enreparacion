<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificarEmailController extends Controller
{
    public function index(){
        return view('auth.verificar-email');
    }
    public function store(){
        
    }
}
