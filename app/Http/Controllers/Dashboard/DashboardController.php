<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneraCodigoReparacion;
use App\Traits\FormateaFechaArgentina;

class DashboardController extends Controller
{
    use GeneraCodigoReparacion, FormateaFechaArgentina;
    
    public function index()
    {
        $user = auth()->user();

        // Obtenemos las reparaciones del negocio con todas sus relaciones cargadas eficientemente (Eager Loading)
        $reparaciones = Reparacion::with([
            'dispositivo.cliente',
            'usuario',
            'negocio'
        ])
        ->where('negocios_id', $user->negocios_id)
        ->latest()
        ->get();

        $transformar = function ($reparacion) {
            $clienteNombre = $reparacion->dispositivo?->cliente?->nombre ?? 'Cliente';
            $marcaModelo = $reparacion->dispositivo?->marca_y_modelo ?? 'Dispositivo';
            $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);

            return [
                'id' => $reparacion->id,
                'codigo_seguimiento' => $codigo,
                'cliente_nombre' => $clienteNombre,
                'cliente_telefono' => $reparacion->dispositivo?->cliente?->telefono ?? 'Sin teléfono',
                'cliente_email' => $reparacion->dispositivo?->cliente?->email ?? 'Sin correo',
                'dispositivo_marca_modelo' => $marcaModelo,
                'imei_o_serie' => $reparacion->dispositivo?->imei_o_serie ?? 'No especificado',
                'clave_de_acceso' => $reparacion->clave_de_acceso ?? 'Sin clave',
                'falla_reportada' => $reparacion->falla_reportada,
                'costo_estimado' => $reparacion->costo_estimado ? ('$' . number_format($reparacion->costo_estimado, 0, ',', '.')) : 'Sin costo estimado',
                'sena' => $reparacion->sena ? ('$' . number_format($reparacion->sena, 0, ',', '.')) : '$0',
                'saldo_pendiente' => '$' . number_format($reparacion->saldo_pendiente, 0, ',', '.'),
                'estado' => ucfirst($reparacion->estado ?? 'Recibido'),
                'estado_slug' => strtolower($reparacion->estado ?? 'recibido'),
                'notas_internas' => $reparacion->notas_internas ?? '',
                'fecha_ingreso' => $this->formatearFechaHoraArgentina($reparacion->created_at),
                'tiempo_relativo' => $this->tiempoTranscurridoArgentina($reparacion->created_at),
                'tecnico' => $reparacion->usuario?->name ?? 'No asignado',
                'search_target' => strtolower($clienteNombre . ' ' . $marcaModelo . ' ' . $reparacion->falla_reportada . ' ' . $codigo . ' ' . $reparacion->id)
            ];
        };

        // Filtramos las reparaciones según su estado para cada columna del tablero
        $reparacionesRecibidas = $reparaciones->filter(function ($reparacion) {
            return strtolower($reparacion->estado ?? '') === 'recibido';
        })->map($transformar)->values();

        $reparacionesEnProceso = $reparaciones->filter(function ($reparacion) {
            return in_array(strtolower($reparacion->estado ?? ''), [
                'en_reparacion',
                'en reparación',
                'en reparacion',
                'en_proceso',
                'en proceso'
            ]);
        })->map($transformar)->values();

        $reparacionesListas = $reparaciones->filter(function ($reparacion) {
            return in_array(strtolower($reparacion->estado ?? ''), [
                'listo',
                'listos',
                'finalizado',
                'terminado'
            ]);
        })->map($transformar)->values();

        return view('home.dashboard', compact(
            'reparaciones',
            'reparacionesRecibidas',
            'reparacionesEnProceso',
            'reparacionesListas'
        ));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'falla_reportada' => ['required', 'string'],
            'sena' => ['nullable', 'numeric'],
            'marca_y_modelo' => ['required', 'string', 'max:255'],
            'clave_de_acceso' => ['nullable', 'string', 'max:255'],
            'imei_o_serie' => ['nullable', 'string', 'max:255'],
            'costo_estimado' => ['nullable', 'numeric'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'falla_reportada.required' => 'La falla reportada es obligatoria.',
            'marca_y_modelo.required' => 'La marca y modelo son obligatorios.',
            'email.email' => 'El correo electrónico no es válido.',
            'sena.numeric' => 'La seña debe ser un valor numérico.',
            'costo_estimado.numeric' => 'El valor debe ser un valor numérico.',
        ]);


        DB::transaction(function () use ($validated){

            $userId = Auth::id();
            $user = User::find($userId);

            $cliente = Cliente::create([
                'negocios_id' => $user->negocios_id, // Solo prueba 
                'nombre' => $validated['nombre'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email']
            ]);

            $dispositivo = Dispositivo::create([
                'clientes_id' => $cliente->id,
                'marca_y_modelo' => $validated['marca_y_modelo'],
                'imei_o_serie' => $validated['imei_o_serie'],
            ]);

            $codigo_seguimiento = $this->generarCodigoSeguimiento($validated['nombre']);

            Reparacion::create([
                'negocios_id' => $user->negocios_id, // Solo prueba
                'dispositivos_id' => $dispositivo->id, // Solo prueba
                'users_id' => $user->id, // Solo prueba
                'falla_reportada' => $validated['falla_reportada'],
                'clave_de_acceso' => $validated['clave_de_acceso'],
                // 'estado' => $validated['estado'],
                'costo_estimado' => $validated['costo_estimado'],
                'sena' => $validated['sena'],
                // 'notas_internas' => $validated['notas_internas'],
                'codigo_seguimiento' => $codigo_seguimiento
            ]);

        });


        return redirect()->route('dashboard.index')->with('success', 'Reparación registrada correctamente.');
    }

    public function updateEstado(Request $request, Reparacion $reparacion)
    {
        $user = auth()->user();

        if ($reparacion->negocios_id !== $user->negocios_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta reparación.'
            ], 403);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', 'in:recibido,en_reparacion,listo,entregado'],
        ]);

        $reparacion->estado = $validated['estado'];
        $reparacion->save();

        $labels = [
            'recibido' => 'Recibidos',
            'en_reparacion' => 'En Reparación',
            'listo' => 'Listos',
            'entregado' => 'Entregado'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado a ' . ($labels[$validated['estado']] ?? $validated['estado']) . '.',
            'estado' => $reparacion->estado,
            'estado_label' => $labels[$validated['estado']] ?? $validated['estado']
        ]);
    }
}
