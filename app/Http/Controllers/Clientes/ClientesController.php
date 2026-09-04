<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Traits\FormateaFechaArgentina;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientesController extends Controller
{
    use FormateaFechaArgentina;

    public function index()
    {
        $user = auth()->user();

        // Obtenemos los clientes del negocio con sus dispositivos y reparaciones cargadas eficientemente
        $clientesRaw = Cliente::with([
            'dispositivos.reparaciones.usuario',
        ])
            ->where('negocios_id', $user->negocios_id)
            ->latest()
            ->get();

        $clientes = $clientesRaw->map(function ($cliente) {
            // Aplanamos todas las reparaciones de todos los dispositivos del cliente
            $todasReparaciones = $cliente->dispositivos->flatMap(function ($disp) {
                return $disp->reparaciones;
            })->sortByDesc('created_at')->values();

            $totalReparaciones = $todasReparaciones->count();

            // Consideramos equipos en taller a aquellos en estado activo ('recibido', 'en_reparacion', o 'listo')
            $reparacionesEnTaller = $todasReparaciones->filter(function ($rep) {
                $estado = strtolower(trim($rep->estado ?? ''));

                return in_array($estado, [
                    'recibido',
                    'en_reparacion',
                    'en reparación',
                    'en reparacion',
                    'en_proceso',
                    'en proceso',
                    'listo',
                    'listos',
                ]);
            });

            $equiposEnTallerCount = $reparacionesEnTaller->count();

            $dispositivosNombres = $cliente->dispositivos->pluck('marca_y_modelo')->filter()->unique()->implode(', ');
            $codigosReparaciones = $todasReparaciones->map(function ($rep) {
                return $rep->codigo_seguimiento ? ('#'.$rep->codigo_seguimiento) : ('#'.$rep->id);
            })->implode(' ');

            $historialReparaciones = $todasReparaciones->map(function ($reparacion) {
                $codigo = $reparacion->codigo_seguimiento ? ('#'.$reparacion->codigo_seguimiento) : ('#'.$reparacion->id);
                $marcaModelo = $reparacion->dispositivo?->marca_y_modelo ?? 'Dispositivo no especificado';

                return [
                    'id' => $reparacion->id,
                    'codigo_seguimiento' => $codigo,
                    'dispositivo_marca_modelo' => $marcaModelo,
                    'imei_o_serie' => $reparacion->dispositivo?->imei_o_serie ?? 'No especificado',
                    'clave_de_acceso' => $reparacion->clave_de_acceso ?? 'Sin clave',
                    'falla_reportada' => $reparacion->falla_reportada,
                    'costo_estimado' => $reparacion->costo_estimado ? ('$'.number_format($reparacion->costo_estimado, 0, ',', '.')) : 'Sin costo',
                    'costo_estimado_num' => (float) ($reparacion->costo_estimado ?? 0),
                    'sena' => $reparacion->sena ? ('$'.number_format($reparacion->sena, 0, ',', '.')) : '$0',
                    'saldo_pendiente' => '$'.number_format($reparacion->saldo_pendiente, 0, ',', '.'),
                    'saldo_pendiente_num' => (float) ($reparacion->saldo_pendiente ?? 0),
                    'estado' => ucfirst($reparacion->estado ?? 'Recibido'),
                    'estado_slug' => strtolower($reparacion->estado ?? 'recibido'),
                    'notas_internas' => $reparacion->notas_internas ?? '',
                    'fecha_ingreso' => $this->formatearFechaHoraArgentina($reparacion->created_at),
                    'tiempo_relativo' => $this->tiempoTranscurridoArgentina($reparacion->created_at),
                    'tecnico' => $reparacion->usuario?->name ?? 'No asignado',
                ];
            })->values();

            $totalGastado = $historialReparaciones->sum('costo_estimado_num');
            $saldoPendienteTotal = $historialReparaciones->sum('saldo_pendiente_num');

            $dispositivosDetallados = $cliente->dispositivos->map(function ($disp) {
                return [
                    'id' => $disp->id,
                    'marca_y_modelo' => $disp->marca_y_modelo ?? 'Sin especificar',
                    'imei_o_serie' => $disp->imei_o_serie ?? 'Sin IMEI/Serie',
                    'total_reparaciones' => $disp->reparaciones->count(),
                ];
            })->values();

            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'telefono' => $cliente->telefono ?? 'Sin teléfono',
                'email' => $cliente->email ?? 'Sin correo',
                'iniciales' => $cliente->iniciales,
                'whatsapp_url' => $cliente->whatsapp_url,
                'total_reparaciones' => $totalReparaciones,
                'total_reparaciones_label' => $totalReparaciones === 1 ? '1 Reparación total' : "{$totalReparaciones} Reparaciones totales",
                'equipos_en_taller' => $equiposEnTallerCount,
                'equipos_en_taller_label' => $equiposEnTallerCount === 1 ? '1 Equipo en taller' : "{$equiposEnTallerCount} Equipos en taller",
                'dispositivos_nombres' => $dispositivosNombres ?: 'Sin dispositivos registrados',
                'dispositivos' => $dispositivosDetallados,
                'total_gastado' => '$'.number_format($totalGastado, 0, ',', '.'),
                'saldo_pendiente_total' => '$'.number_format($saldoPendienteTotal, 0, ',', '.'),
                'fecha_registro' => $this->formatearSoloFechaArgentina($cliente->created_at),
                'tiempo_registro_relativo' => $this->tiempoTranscurridoArgentina($cliente->created_at, 'Registrado '),
                'historial_reparaciones' => $historialReparaciones,
                'search_target' => strtolower(
                    $cliente->nombre.' '.
                    $cliente->telefono.' '.
                    ($cliente->email ?? '').' '.
                    $dispositivosNombres.' '.
                    $codigosReparaciones
                ),
            ];
        })->values();

        $totalClientes = $clientes->count();
        $totalEquiposEnTaller = $clientes->sum('equipos_en_taller');
        $totalReparacionesNegocio = $clientes->sum('total_reparaciones');

        return view('home.clientes', compact(
            'clientes',
            'totalClientes',
            'totalEquiposEnTaller',
            'totalReparacionesNegocio'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
        ]);

        Cliente::create([
            'negocios_id' => $user->negocios_id,
            'nombre' => $validated['nombre'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'] ?? null,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $user = auth()->user();

        if ($cliente->negocios_id !== $user->negocios_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar este cliente.',
            ], 403);
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
        ]);

        $cliente->nombre = $validated['nombre'];
        $cliente->telefono = $validated['telefono'];
        $cliente->email = $validated['email'] ?? null;
        $cliente->save();

        return response()->json([
            'success' => true,
            'message' => 'Datos del cliente actualizados correctamente.',
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'telefono' => $cliente->telefono,
                'email' => $cliente->email ?? 'Sin correo',
                'iniciales' => $cliente->iniciales,
                'whatsapp_url' => $cliente->whatsapp_url,
            ],
        ]);
    }
}

