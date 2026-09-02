<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\OlvideContraseña;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class OlvideController extends Controller
{
    public function index()
    {
        return view('auth.olvide');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email']
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.exists' => 'Revisa el email que introduciste',
        ]);

        $user = User::where('email', $validated['email'])->first();

        $status = Password::sendResetLink(
            $request->only('email'),
        );

        return redirect()->route('login')->with('success', 'Revisa tu Email!, Enviamos unas instrucciones');
    }
}
