<?php

namespace App\Http\Controllers\Reparacionse;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Reparacion;
use App\Services\NotificacionService;
use App\Traits\FormateaFechaArgentina;
use App\Traits\GeneraCodigoReparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReparacionesController extends Controller
{
    use FormateaFechaArgentina, GeneraCodigoReparacion;

    public function index()
    {
        $user = auth()->user();

        // Obtenemos todas las reparaciones del negocio con sus relaciones
        $reparacionesRaw = Reparacion::with([
            'dispositivo.cliente',
            'usuario',
            'negocio',
        ])
            ->where('negocios_id', $user->negocios_id)
            ->latest()
            ->get();

        $negocio = $user->negocio;
        $negocioNombre = $negocio?->nombre ?? 'EnReparación';
        $negocioTelefono = $negocio?->telefono ?? '';
        $negocioDireccion = $negocio?->direccion ?? '';

        $reparaciones = $reparacionesRaw->map(function ($rep) use ($negocioNombre, $negocioTelefono, $negocioDireccion) {
            $cliente = $rep->dispositivo?->cliente;
            $clienteNombre = $cliente?->nombre ?? 'Cliente no especificado';
            $clienteTelefono = $cliente?->telefono ?? 'Sin teléfono';
            $clienteEmail = $cliente?->email ?? 'Sin correo';
            $clienteIniciales = $cliente?->iniciales ?? 'CL';
            $whatsappUrl = $cliente?->whatsapp_url ?? '#';

            $marcaModelo = $rep->dispositivo?->marca_y_modelo ?? 'Dispositivo no especificado';
            $imeiSerie = $rep->dispositivo?->imei_o_serie ?? 'No especificado';
            $codigo = $rep->codigo_seguimiento ? ('#'.$rep->codigo_seguimiento) : ('#'.$rep->id);
            $codigoLimpio = $rep->codigo_seguimiento ?? (string) $rep->id;

            // Normalización de estados
            $estadoCrudo = strtolower(trim($rep->estado ?? 'recibido'));
            $estadoSlug = 'recibido';
            $estadoLabel = 'Recibido';
            $dotColor = 'bg-[#0081cc]'; // Azul primario
            $estadoBadgeBg = 'bg-[#0081cc]/15 text-[#33b4ff] border-[#0081cc]/30';

            if (in_array($estadoCrudo, ['en_reparacion', 'en reparación', 'en reparacion', 'en_proceso', 'en proceso', 'proceso'])) {
                $estadoSlug = 'en_reparacion';
                $estadoLabel = 'En reparación';
                $dotColor = 'bg-amber-400';
                $estadoBadgeBg = 'bg-amber-500/15 text-amber-400 border-amber-500/30';
            } elseif (in_array($estadoCrudo, ['listo', 'listos', 'lista', 'listas', 'finalizado', 'terminado'])) {
                $estadoSlug = 'listo';
                $estadoLabel = 'Listo para entrega';
                $dotColor = 'bg-teal-400';
                $estadoBadgeBg = 'bg-teal-500/15 text-teal-400 border-teal-500/30';
            } elseif (in_array($estadoCrudo, ['entregado', 'entregados', 'entregada', 'entregadas'])) {
                $estadoSlug = 'entregado';
                $estadoLabel = 'Entregado';
                $dotColor = 'bg-emerald-400';
                $estadoBadgeBg = 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
            } elseif (in_array($estadoCrudo, ['cancelado', 'cancelados', 'cancelada', 'canceladas', 'anulado'])) {
                $estadoSlug = 'cancelado';
                $estadoLabel = 'Cancelado';
                $dotColor = 'bg-rose-500';
                $estadoBadgeBg = 'bg-rose-500/15 text-rose-400 border-rose-500/30';
            }

            $costoNum = (float) ($rep->costo_estimado ?? 0);
            $senaNum = (float) ($rep->sena ?? 0);
            $saldoNum = (float) ($rep->saldo_pendiente ?? max(0, $costoNum - $senaNum));

            $fechaCarbon = $this->aCarbonArgentina($rep->created_at);
            $fechaCorta = $fechaCarbon ? $fechaCarbon->translatedFormat('d M, Y') : '-';
            $fechaHora = $this->formatearFechaHoraArgentina($rep->created_at);
            $tiempoRelativo = $this->tiempoTranscurridoArgentina($rep->created_at);

            return [
                'id' => $rep->id,
                'codigo_seguimiento' => $codigo,
                'codigo_limpio' => $codigoLimpio,
                'cliente_id' => $cliente?->id,
                'cliente_nombre' => $clienteNombre,
                'cliente_telefono' => $clienteTelefono,
                'cliente_email' => $clienteEmail,
                'cliente_iniciales' => $clienteIniciales,
                'whatsapp_url' => $whatsappUrl,
                'dispositivo_marca_modelo' => $marcaModelo,
                'imei_o_serie' => $imeiSerie,
                'clave_de_acceso' => $rep->clave_de_acceso ?: 'Sin clave',
                'falla_reportada' => $rep->falla_reportada ?: 'No especificada',
                'costo_estimado' => $costoNum > 0 ? ('$'.number_format($costoNum, 0, ',', '.')) : 'Sin costo',
                'costo_estimado_num' => $costoNum,
                'sena' => $senaNum > 0 ? ('$'.number_format($senaNum, 0, ',', '.')) : '$0',
                'sena_num' => $senaNum,
                'saldo_pendiente' => '$'.number_format($saldoNum, 0, ',', '.'),
                'saldo_pendiente_num' => $saldoNum,
                'esta_saldado' => $saldoNum <= 0 && $costoNum > 0,
                'estado' => $estadoLabel,
                'estado_slug' => $estadoSlug,
                'dot_color' => $dotColor,
                'estado_badge_bg' => $estadoBadgeBg,
                'notas_internas' => $rep->notas_internas ?? '',
                'fecha_corta' => $fechaCorta,
                'fecha_hora' => $fechaHora,
                'tiempo_relativo' => $tiempoRelativo,
                'tecnico' => $rep->usuario?->name ?? 'Taller Central',
                'negocio_nombre' => $negocioNombre,
                'negocio_telefono' => $negocioTelefono,
                'negocio_direccion' => $negocioDireccion,
                'search_target' => strtolower(
                    $clienteNombre.' '.
                    $codigo.' '.
                    $codigoLimpio.' '.
                    $marcaModelo.' '.
                    $imeiSerie.' '.
                    $clienteTelefono.' '.
                    $rep->falla_reportada.' '.
                    ($rep->notas_internas ?? '').' '.
                    $estadoLabel.' '.
                    ($rep->usuario?->name ?? '')
                ),
            ];
        })->values();

        // Contadores para las Píldoras de Filtro Rápido
        $totalTodas = $reparaciones->count();
        $totalEntregadas = $reparaciones->where('estado_slug', 'entregado')->count();
        $totalEnReparacion = $reparaciones->where('estado_slug', 'en_reparacion')->count();
        $totalRecibidas = $reparaciones->where('estado_slug', 'recibido')->count();
        $totalListas = $reparaciones->where('estado_slug', 'listo')->count();
        $totalCanceladas = $reparaciones->where('estado_slug', 'cancelado')->count();

        // Resumen económico acumulado
        $totalFacturado = $reparaciones->sum('costo_estimado_num');
        $totalPendienteCobro = $reparaciones->sum('saldo_pendiente_num');

        return view('home.reparaciones', compact(
            'reparaciones',
            'totalTodas',
            'totalEntregadas',
            'totalEnReparacion',
            'totalRecibidas',
            'totalListas',
            'totalCanceladas',
            'totalFacturado',
            'totalPendienteCobro',
            'negocioNombre',
            'negocioTelefono',
            'negocioDireccion'
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
            'notas_internas' => ['nullable', 'string'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'falla_reportada.required' => 'La falla reportada es obligatoria.',
            'marca_y_modelo.required' => 'La marca y modelo son obligatorios.',
            'email.email' => 'El correo electrónico no es válido.',
            'sena.numeric' => 'La seña debe ser un valor numérico.',
            'costo_estimado.numeric' => 'El valor estimado debe ser numérico.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = auth()->user();

            $cliente = Cliente::create([
                'negocios_id' => $user->negocios_id,
                'nombre' => $validated['nombre'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'] ?? null,
            ]);

            $dispositivo = Dispositivo::create([
                'clientes_id' => $cliente->id,
                'marca_y_modelo' => $validated['marca_y_modelo'],
                'imei_o_serie' => $validated['imei_o_serie'] ?? null,
            ]);

            $codigoSeguimiento = $this->generarCodigoSeguimiento($validated['nombre']);

            Reparacion::create([
                'negocios_id' => $user->negocios_id,
                'dispositivos_id' => $dispositivo->id,
                'users_id' => $user->id,
                'falla_reportada' => $validated['falla_reportada'],
                'clave_de_acceso' => $validated['clave_de_acceso'] ?? null,
                'costo_estimado' => $validated['costo_estimado'] ?? null,
                'sena' => $validated['sena'] ?? null,
                'notas_internas' => $validated['notas_internas'] ?? null,
                'codigo_seguimiento' => $codigoSeguimiento,
                'estado' => 'recibido',
            ]);
        });

        return redirect()->route('reparaciones.index')->with('success', 'Reparación creada y registrada correctamente.');
    }

    public function updateEstado(Request $request, Reparacion $reparacion, NotificacionService $notificacionService)
    {
        $user = auth()->user();

        if ($reparacion->negocios_id !== $user->negocios_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta reparación.',
            ], 403);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', 'in:recibido,en_reparacion,listo,entregado,cancelado'],
            'enviar_email' => ['nullable', 'boolean'],
            'mensaje_personalizado' => ['nullable', 'string', 'max:2000'],
        ]);

        $reparacion->estado = $validated['estado'];
        $reparacion->save();

        $labels = [
            'recibido' => 'Recibido',
            'en_reparacion' => 'En reparación',
            'listo' => 'Listo para entrega',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];

        $dotColors = [
            'recibido' => 'bg-[#0081cc]',
            'en_reparacion' => 'bg-amber-400',
            'listo' => 'bg-teal-400',
            'entregado' => 'bg-emerald-400',
            'cancelado' => 'bg-rose-500',
        ];

        $emailEnviado = false;
        $emailMensaje = null;

        if (!empty($validated['enviar_email']) && $validated['estado'] !== 'cancelado') {
            $reparacion->loadMissing(['dispositivo.cliente', 'negocio', 'usuario']);
            $resultadoNotificacion = $notificacionService->enviarNotificacionEstado(
                reparacion: $reparacion,
                nuevoEstado: $validated['estado'],
                usuario: $user,
                mensajePersonalizado: $validated['mensaje_personalizado'] ?? null
            );

            $emailEnviado = $resultadoNotificacion['enviado'] ?? false;
            $emailMensaje = $resultadoNotificacion['message'] ?? null;
        }

        $mensajeExito = 'Estado actualizado a ' . ($labels[$validated['estado']] ?? $validated['estado']) . '.';
        if ($emailEnviado) {
            $mensajeExito .= ' Se envió la notificación por correo al cliente.';
        } elseif (!empty($validated['enviar_email']) && !$emailEnviado && $emailMensaje) {
            $mensajeExito .= ' (Aviso de email: ' . $emailMensaje . ')';
        }

        return response()->json([
            'success' => true,
            'message' => $mensajeExito,
            'estado' => $labels[$validated['estado']] ?? $validated['estado'],
            'estado_slug' => $validated['estado'],
            'dot_color' => $dotColors[$validated['estado']] ?? 'bg-primary',
            'email_enviado' => $emailEnviado,
            'email_mensaje' => $emailMensaje,
        ]);
    }

    public function updateNotas(Request $request, Reparacion $reparacion)
    {
        $user = auth()->user();

        if ($reparacion->negocios_id !== $user->negocios_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para modificar esta reparación.',
            ], 403);
        }

        $validated = $request->validate([
            'notas_internas' => ['nullable', 'string', 'max:2000'],
        ]);

        $reparacion->notas_internas = $validated['notas_internas'] ?? '';
        $reparacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Notas técnicas actualizadas correctamente.',
            'notas_internas' => $reparacion->notas_internas,
        ]);
    }
}
