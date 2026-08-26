<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class VerificarEmailController extends Controller
{
    /**
     * Muestra la vista de verificación de correo electrónico.
     */
    public function index()
    {
        $user = Auth::user();

        // Si el usuario ya está verificado, redirigir al siguiente paso
        if ($user && $user->email_verified_at) {
            return $user->negocios_id
                ? redirect()->route('dashboard.index')
                : redirect()->route('negocios');
        }

        return view('auth.verificar-email');
    }

    /**
     * Procesa y valida el código de verificación ingresado por el usuario.
     */
    public function store(Request $request)
    {
        // Obtener el código ya sea del input 'code' o del array 'digits'
        $code = $request->input('code');
        if (empty($code) && is_array($request->input('digits'))) {
            $code = implode('', $request->input('digits'));
        }

        // Asignar el código unificado al request para validarlo
        $request->merge(['code' => $code]);

        $request->validate([
            'code' => ['required', 'string', 'digits:5'],
        ], [
            'code.required' => 'Debes ingresar el código de verificación.',
            'code.digits' => 'El código debe tener exactamente 5 dígitos.',
        ]);

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => 'Debes iniciar sesión para verificar tu cuenta.']);
        }

        // Buscar el último código de verificación emitido para el usuario
        $verificationCode = $user->emailVerificationCodes()->latest()->first();

        if (! $verificationCode) {
            return back()->withErrors(['code' => 'No tienes un código de verificación activo. Solicita uno nuevo.']);
        }

        // Verificar si el código ya expiró
        if ($verificationCode->expires_at->isPast()) {
            return back()->withErrors(['code' => 'El código ha expirado. Por favor, solicita uno nuevo.']);
        }

        // Comprobar si el código coincide con el hash guardado
        if (! Hash::check($code, $verificationCode->code)) {
            return back()->withErrors(['code' => 'El código de verificación ingresado es incorrecto.']);
        }

        // Marcar el email como verificado
        $user->email_verified_at = now();
        $user->save();

        // Limpiar los códigos de verificación utilizados
        $user->emailVerificationCodes()->delete();

        // Redirigir a registrar el negocio si aún no tiene uno
        if (! $user->negocios_id) {
            return redirect()->route('negocios')->with('success', '¡Email verificado con éxito! Ahora registra tu negocio.');
        }

        return redirect()->route('dashboard.index')->with('success', '¡Email verificado correctamente!');
    }

    /**
     * Reenvía un nuevo código de verificación por correo electrónico.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Eliminar códigos anteriores
        $user->emailVerificationCodes()->delete();

        // Generar un nuevo código de 5 dígitos
        $code = random_int(10000, 99999);

        $user->emailVerificationCodes()->create([
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Enviar el correo
        Mail::to($user->email)->send(new VerifyEmailCode($code, $user));

        return back()->with('success', '¡Te hemos enviado un nuevo código a tu correo!');
    }
}
