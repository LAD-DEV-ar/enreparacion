<?php

namespace App\Mail;

use App\Models\Reparacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstadoReparacionActualizado extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Reparacion $reparacion,
        public string $nuevoEstado,
        public string $asunto,
        public string $mensajeProcesado,
        public ?string $mensajePersonalizado = null
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reparaciones.estado-actualizado',
            with: [
                'reparacion' => $this->reparacion,
                'nuevoEstado' => $this->nuevoEstado,
                'asunto' => $this->asunto,
                'mensajeProcesado' => $this->mensajeProcesado,
                'mensajePersonalizado' => $this->mensajePersonalizado,
                'negocio' => $this->reparacion->negocio,
                'cliente' => $this->reparacion->cliente,
                'dispositivo' => $this->reparacion->dispositivo,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
