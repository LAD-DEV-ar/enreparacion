@extends('emails.layouts.master')

@section('title', 'Recupera tu contraseña - EnReparacion')

@section('preheader', 'Estas son las instrucciones para recuperar tu contraseña:')

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
                    Recupera tu contraseña
                </h1>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-bottom: 28px;">
                <p style="margin: 0; font-size: 15px; color: #aab6c4; line-height: 1.6; text-align: center; max-width: 440px;">
                    Hola {{ $user->name }}, Para recuperar tu contraseña en <strong style="color: #ffffff;">EnReparacion</strong>. Ingresa al siguiente enlace:
                </p>
            </td>
        </tr>
    </table>

    {{-- Botón CTA: Enlace de Verificación --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
        <tr>
            <td align="center">
                <p style="margin: 0 0 18px 0; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #6b7280;">
                    ENLACE DE VERIFICACIÓN
                </p>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="border-radius: 12px; background-color: #0081cc;">
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                style="
                                    display: inline-block;
                                    padding: 14px 36px;
                                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                                    font-size: 15px;
                                    font-weight: 700;
                                    color: #ffffff;
                                    text-decoration: none;
                                    border-radius: 12px;
                                    letter-spacing: 0.3px;
                                "
                            >
                                Recuperar contraseña
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Tiempo de expiración y aviso de seguridad --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="background-color: rgba(255, 255, 255, 0.03); border-radius: 10px; padding: 14px 18px;">
                <p style="margin: 0; font-size: 13px; color: #aab6c4; line-height: 1.5;">
                    Si no funciona el boton copia y pega este enlace en tu buscador: {{ $url }}
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding-top: 24px; text-align: center;">
                <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                    Si no solicitaste la recuperacion de tu contraseña, puedes ignorar este correo.
                </p>
            </td>
        </tr>
    </table>
@endsection
