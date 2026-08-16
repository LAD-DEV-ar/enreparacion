<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name', 'EnReparacion'))</title>
    <style>
        /* Resets de clientes de correo */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; background-color: #131a22; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #131a22; color: #f5f7fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    
    {{-- Texto de previsualización para clientes de correo --}}
    <div style="display: none; font-size: 1px; color: #131a22; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        @yield('preheader', 'Notificación de EnReparacion')
    </div>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #131a22; padding: 40px 15px;">
        <tr>
            <td align="center">
                
                {{-- Contenedor Principal (Max 580px) --}}
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; width: 100%;">
                    
                    {{-- Encabezado / Logo --}}
                    <tr>
                        <td align="center" style="padding-bottom: 28px;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <span style="font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; text-decoration: none;">
                                            En<span style="color: #0081cc;">Reparacion</span>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Tarjeta de Contenido --}}
                    <tr>
                        <td style="background-color: #1c2530; border: 1px solid #2e3a4b; border-radius: 20px; padding: 40px 32px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pie de página --}}
                    <tr>
                        <td align="center" style="padding-top: 30px; text-align: center;">
                            <p style="margin: 0 0 10px 0; font-size: 12px; color: #6b7280; line-height: 1.5;">
                                Este es un correo automático enviado por <strong>EnReparacion</strong>. Por favor, no respondas a este mensaje.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #4b5563;">
                                &copy; {{ date('Y') }} EnReparacion. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
