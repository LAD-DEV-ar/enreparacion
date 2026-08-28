<?php

namespace App\Services;

use App\Mail\EstadoReparacionActualizado;
use App\Models\Notificacion;
use App\Models\NotificacionCliente;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificacionService
{
    /**
     * Mapeo de estados y sus etiquetas legibles.
     */
    public const ESTADO_LABELS = [
        'recibido' => 'Recibido',
        'en_reparacion' => 'En reparación',
        'listo' => 'Listo para entrega',
        'entregado' => 'Entregado',
    ];

    /**
     * Envía la notificación de cambio de estado al cliente si posee email y registra la auditoría.
     */
    public function enviarNotificacionEstado(
        Reparacion $reparacion,
        string $nuevoEstado,
        User $usuario,
        ?string $mensajePersonalizado = null
    ): array {
        $cliente = $reparacion->dispositivo?->cliente;
        $email = trim($cliente?->email ?? '');

        // Validación de existencia de correo
        if (empty($email) || strtolower($email) === 'sin correo' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'enviado' => false,
                'message' => 'El cliente no tiene un correo electrónico válido registrado.',
            ];
        }

        // Obtener plantilla personalizada o por defecto
        $plantilla = $this->obtenerPlantilla($reparacion->negocios_id, 'email', $nuevoEstado);

        // Interpolar variables dinámicas
        $asuntoFinal = $this->reemplazarVariables($plantilla['titulo'], $reparacion, $nuevoEstado);
        $mensajeFinal = $this->reemplazarVariables($plantilla['mensaje'], $reparacion, $nuevoEstado);

        $estadoEnvio = 'enviado';
        $errorMensaje = null;
        $envioExitoso = false;

        try {
            // Envío del correo con Mailable
            Mail::to($email)->send(new EstadoReparacionActualizado(
                reparacion: $reparacion,
                nuevoEstado: $nuevoEstado,
                asunto: $asuntoFinal,
                mensajeProcesado: $mensajeFinal,
                mensajePersonalizado: $mensajePersonalizado
            ));

            $envioExitoso = true;
        } catch (Throwable $e) {
            $estadoEnvio = 'fallido';
            $errorMensaje = $e->getMessage();
            Log::error("Error al enviar email de notificación de estado a {$email}: " . $e->getMessage(), [
                'reparacion_id' => $reparacion->id,
                'nuevo_estado' => $nuevoEstado,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Registro inmutable de auditoría en notificacion_clientes
        try {
            NotificacionCliente::create([
                'negocios_id' => $reparacion->negocios_id,
                'reparaciones_id' => $reparacion->id,
                'clientes_id' => $cliente?->id,
                'users_id' => $usuario->id,
                'canal' => 'email',
                'destinatario' => $email,
                'asunto' => $asuntoFinal,
                'mensaje' => $mensajePersonalizado ? "{$mensajeFinal}\n\n[Nota técnica]: {$mensajePersonalizado}" : $mensajeFinal,
                'estado_envio' => $estadoEnvio,
                'error_mensaje' => $errorMensaje,
                'metadata' => [
                    'nuevo_estado' => $nuevoEstado,
                    'codigo_seguimiento' => $reparacion->codigo_seguimiento,
                    'mailer' => config('mail.default'),
                ],
            ]);
        } catch (Throwable $e) {
            Log::error("Error al registrar auditoría en notificacion_clientes: " . $e->getMessage());
        }

        if ($envioExitoso) {
            return [
                'success' => true,
                'enviado' => true,
                'destinatario' => $email,
                'message' => "Correo electrónico enviado correctamente a {$email}.",
            ];
        }

        return [
            'success' => false,
            'enviado' => false,
            'destinatario' => $email,
            'message' => "No se pudo enviar el correo a {$email}. Se guardó el registro de auditoría con el error.",
            'error' => $errorMensaje,
        ];
    }

    /**
     * Obtiene la plantilla configurada en base de datos o retorna la plantilla predeterminada del sistema.
     */
    public function obtenerPlantilla(int $negociosId, string $canal, string $evento): array
    {
        $plantillaDB = Notificacion::where('negocios_id', $negociosId)
            ->where('canal', $canal)
            ->where('evento', $evento)
            ->where('activo', true)
            ->first();

        if ($plantillaDB) {
            return [
                'titulo' => $plantillaDB->titulo ?? $this->obtenerTituloDefault($evento),
                'mensaje' => $plantillaDB->mensaje,
            ];
        }

        return $this->obtenerPlantillaDefault($evento);
    }

    /**
     * Reemplaza los marcadores de posición con los datos reales de la reparación.
     */
    public function reemplazarVariables(string $texto, Reparacion $reparacion, string $nuevoEstado): string
    {
        $cliente = $reparacion->dispositivo?->cliente;
        $dispositivo = $reparacion->dispositivo;
        $negocio = $reparacion->negocio;

        $costoNum = (float)($reparacion->costo_estimado ?? 0);
        $senaNum = (float)($reparacion->sena ?? 0);
        $saldoNum = (float)($reparacion->saldo_pendiente ?? max(0, $costoNum - $senaNum));

        $codigo = $reparacion->codigo_seguimiento ? ('#' . $reparacion->codigo_seguimiento) : ('#' . $reparacion->id);
        $estadoLabel = self::ESTADO_LABELS[$nuevoEstado] ?? ucfirst($nuevoEstado);

        $variables = [
            '{cliente_nombre}' => $cliente?->nombre ?? 'Cliente',
            '{equipo}' => $dispositivo?->marca_y_modelo ?? 'Dispositivo',
            '{codigo_seguimiento}' => $codigo,
            '{estado}' => $estadoLabel,
            '{falla}' => $reparacion->falla_reportada ?? 'No especificada',
            '{costo_estimado}' => $costoNum > 0 ? ('$' . number_format($costoNum, 0, ',', '.')) : 'A convenir',
            '{sena}' => $senaNum > 0 ? ('$' . number_format($senaNum, 0, ',', '.')) : '$0',
            '{saldo_pendiente}' => '$' . number_format($saldoNum, 0, ',', '.'),
            '{negocio_nombre}' => $negocio?->nombre ?? config('app.name', 'EnReparacion'),
            '{negocio_telefono}' => $negocio?->telefono ?? '',
            '{negocio_direccion}' => $negocio?->direccion ?? '',
        ];

        return str_replace(array_keys($variables), array_values($variables), $texto);
    }

    /**
     * Plantillas predeterminadas de fábrica para cada estado.
     */
    private function obtenerPlantillaDefault(string $evento): array
    {
        $defaults = [
            'recibido' => [
                'titulo' => 'Hemos recibido tu equipo {equipo} ({codigo_seguimiento}) - {negocio_nombre}',
                'mensaje' => "Te confirmamos que hemos recibido tu {equipo} en nuestro taller bajo el código {codigo_seguimiento}.\n\nPróximamente iniciaremos la revisión técnica y te mantendremos al tanto de cada novedad.",
            ],
            'en_reparacion' => [
                'titulo' => 'Tu equipo {equipo} está en reparación ({codigo_seguimiento}) - {negocio_nombre}',
                'mensaje' => "¡Buenas noticias! Nuestro equipo técnico ha comenzado a trabajar en tu {equipo}.\n\nEstamos aplicando los procedimientos necesarios para solucionar la falla reportada. Te notificaremos apenas finalicemos.",
            ],
            'listo' => [
                'titulo' => '¡Tu equipo {equipo} ya está listo para retirar! ({codigo_seguimiento}) - {negocio_nombre}',
                'mensaje' => "¡Excelente noticia! La reparación de tu {equipo} ha concluido con éxito.\n\nYa puedes pasar a retirarlo por nuestro taller. Saldo pendiente a abonar al momento del retiro: {saldo_pendiente}.\n\n¡Te esperamos!",
            ],
            'entregado' => [
                'titulo' => 'Tu equipo {equipo} ha sido entregado ({codigo_seguimiento}) - {negocio_nombre}',
                'mensaje' => "Confirmamos que tu {equipo} ha sido entregado exitosamente.\n\nMuchas gracias por confiar en nosotros. ¡Ante cualquier consulta o inquietud, estamos a tu total disposición!",
            ],
        ];

        return $defaults[$evento] ?? [
            'titulo' => 'Actualización de estado de tu reparación {codigo_seguimiento} - {negocio_nombre}',
            'mensaje' => "El estado de tu reparación ({equipo}) ha cambiado a: {estado}.\n\nSaludos,\n{negocio_nombre}",
        ];
    }

    /**
     * Título predeterminado según el evento.
     */
    private function obtenerTituloDefault(string $evento): string
    {
        return $this->obtenerPlantillaDefault($evento)['titulo'] ?? 'Actualización de reparación';
    }
}
