@extends('emails.layouts.master')

@section('title', 'Verifica tu correo electrónico - EnReparacion')

@section('preheader', 'Tu código de verificación de EnReparacion es: ' . $code)

@section('content')
    {{-- Encabezado del Mensaje --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding-bottom: 24px;">
                {{-- Círculo con Ícono de Email --}}
                <div style="display: inline-block; width: 56px; height: 56px; line-height: 56px; border-radius: 16px; background-color: rgba(0, 129, 204, 0.15); border: 1px solid rgba(0, 129, 204, 0.3); text-align: center;">
                    <span style="font-size: 26px; line-height: 56px;">✉️</span>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 12px;">
                <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; text-align: center;">
                    Verifica tu correo electrónico
                </h1>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 28px;">
                <p style="margin: 0; font-size: 15px; color: #aab6c4; line-height: 1.6; text-align: center; max-width: 440px;">
                    Hola <strong style="color: #ffffff;">{{ $user->name ?? 'allí' }}</strong>, gracias por registrarte en <strong style="color: #ffffff;">EnReparacion</strong>. Usa el siguiente código para verificar tu cuenta y continuar:
                </p>
            </td>
        </tr>
    </table>

    {{-- Bloque Destacado del Código OTP --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
        <tr>
            <td align="center">
                <div style="background-color: #131a22; border: 1px solid #364252; border-radius: 14px; padding: 20px 24px; text-align: center; max-width: 300px;">
                    <span style="display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #6b7280; margin-bottom: 8px;">
                        CÓDIGO DE VERIFICACIÓN
                    </span>
                    <span style="font-family: 'Courier New', Courier, monospace, sans-serif; font-size: 36px; font-weight: 800; letter-spacing: 10px; color: #33b4ff; padding-left: 10px;">
                        {{ $code }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tiempo de expiración y aviso de seguridad --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="background-color: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 14px 18px;">
                <p style="margin: 0; font-size: 13px; color: #aab6c4; line-height: 1.5;">
                    ⏱ Este código tiene una validez de <strong style="color: #f5f7fa;">10 minutos</strong>.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 24px; text-align: center;">
                <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                    Si no creaste una cuenta en EnReparacion, puedes desestimar este correo con total tranquilidad.
                </p>
            </td>
        </tr>
    </table>
@endsection
