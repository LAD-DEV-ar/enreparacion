@extends('emails.layouts.master')

@section('title', $asunto)

@php
    $estadoSlug = strtolower(trim($nuevoEstado));
    $badgeBg = '#0081cc';
    $badgeText = '#ffffff';
    $estadoTitulo = 'Actualización de Estado';
    $estadoIcon = '🔧';

    if (in_array($estadoSlug, ['en_reparacion', 'en reparación', 'en reparacion', 'en_proceso', 'en proceso'])) {
        $badgeBg = '#f59e0b';
        $badgeText = '#1c2530';
        $estadoTitulo = 'En Reparación';
        $estadoIcon = '⚙️';
    } elseif (in_array($estadoSlug, ['listo', 'listos', 'finalizado', 'terminado'])) {
        $badgeBg = '#10b981';
        $badgeText = '#ffffff';
        $estadoTitulo = '¡Listo para retirar!';
        $estadoIcon = '✅';
    } elseif (in_array($estadoSlug, ['entregado', 'entregados'])) {
        $badgeBg = '#059669';
        $badgeText = '#ffffff';
        $estadoTitulo = 'Equipo Entregado';
        $estadoIcon = '📦';
    } elseif ($estadoSlug === 'recibido') {
        $badgeBg = '#0081cc';
        $badgeText = '#ffffff';
        $estadoTitulo = 'Equipo Recibido';
        $estadoIcon = '📥';
    }
    
    $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);
    $clienteNombre = $cliente?->nombre ?? 'Estimado/a cliente';
    $dispositivoNombre = $dispositivo?->marca_y_modelo ?? 'Dispositivo';
    $negocioNombre = $negocio?->nombre ?? config('app.name', 'EnReparacion');
    $costo = (float)($reparacion->costo_estimado ?? 0);
    $sena = (float)($reparacion->sena ?? 0);
    $saldo = max(0, $costo - $sena);
@endphp

@section('preheader', "Actualización de tu reparación {$codigo} en {$negocioNombre}")

@section('content')
    {{-- Badge de Estado y Código --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
        <tr>
            <td>
                <span style="display: inline-block; background-color: {{ $badgeBg }}; color: {{ $badgeText }}; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; padding: 6px 14px; border-radius: 9999px;">
                    {{ $estadoIcon }} {{ $estadoTitulo }}
                </span>
            </td>
            <td align="right">
                <span style="display: inline-block; background-color: #273343; color: #38bdf8; border: 1px solid #38bdf8; font-size: 13px; font-weight: 700; padding: 5px 12px; border-radius: 8px;">
                    {{ $codigo }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Saludo y Título --}}
    <h1 style="color: #ffffff; font-size: 22px; font-weight: 800; margin: 0 0 16px 0; line-height: 1.3;">
        Hola, {{ $clienteNombre }} 👋
    </h1>

    {{-- Mensaje Principal --}}
    <div style="color: #e2e8f0; font-size: 15px; line-height: 1.6; margin-bottom: 24px; white-space: pre-line;">
        {{ $mensajeProcesado }}
    </div>

    @if(!empty($mensajePersonalizado))
        {{-- Nota Adicional Personalizada por el Técnico --}}
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #141c25; border-radius: 8px; margin-bottom: 24px;">
            <tr>
                <td style="padding: 14px 16px;">
                    <span style="display: block; font-size: 12px; font-weight: 700; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                        Nota del taller:
                    </span>
                    <p style="color: #f1f5f9; font-size: 14px; margin: 0; line-height: 1.5; font-style: italic;">
                        "{{ $mensajePersonalizado }}"
                    </p>
                </td>
            </tr>
        </table>
    @endif

    {{-- Resumen de la Orden / Dispositivo --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #273343; border-radius: 12px; border: 1px solid #364457; margin-bottom: 26px;">
        <tr>
            <td style="padding: 18px 20px;">
                <span style="display: block; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px;">
                    Detalles de la Reparación
                </span>

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #94a3b8; width: 40%;">Equipo:</td>
                        <td style="padding: 4px 0; font-size: 14px; font-weight: 700; color: #ffffff; text-align: right;">{{ $dispositivoNombre }}</td>
                    </tr>
                    @if(!empty($reparacion->falla_reportada))
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #94a3b8;">Falla:</td>
                        <td style="padding: 4px 0; font-size: 14px; font-weight: 500; color: #e2e8f0; text-align: right;">{{ $reparacion->falla_reportada }}</td>
                    </tr>
                    @endif
                    @if($costo > 0)
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #94a3b8;">Valor Estimado:</td>
                        <td style="padding: 4px 0; font-size: 14px; font-weight: 600; color: #ffffff; text-align: right;">${{ number_format($costo, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($sena > 0)
                    <tr>
                        <td style="padding: 4px 0; font-size: 14px; color: #94a3b8;">Seña Abonada:</td>
                        <td style="padding: 4px 0; font-size: 14px; font-weight: 600; color: #10b981; text-align: right;">-${{ number_format($sena, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($costo > 0)
                    <tr>
                        <td style="padding: 8px 0 0 0; border-top: 1px solid #364457; font-size: 15px; font-weight: 700; color: #ffffff;">Saldo a Abonar:</td>
                        <td style="padding: 8px 0 0 0; border-top: 1px solid #364457; font-size: 16px; font-weight: 800; color: {{ $saldo > 0 ? '#f59e0b' : '#10b981' }}; text-align: right;">
                            ${{ number_format($saldo, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Bloque de Información del Taller / Negocio --}}
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #2e3a4b; padding-top: 20px;">
        <tr>
            <td>
                <p style="margin: 0 0 4px 0; font-size: 13px; font-weight: 700; color: #ffffff;">
                    {{ $negocioNombre }}
                </p>
                @if(!empty($negocio?->direccion))
                    <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">
                        📍 {{ $negocio->direccion }}
                    </p>
                @endif
                @if(!empty($negocio?->telefono))
                    <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                        📞 {{ $negocio->telefono }}
                    </p>
                @endif
            </td>
        </tr>
    </table>
@endsection
