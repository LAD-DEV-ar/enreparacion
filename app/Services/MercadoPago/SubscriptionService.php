<?php

namespace App\Services\MercadoPago;

use App\Exceptions\MercadoPagoException;
use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Support\Carbon;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Exceptions\MPApiException;

class SubscriptionService
{
    public function __construct(
        private readonly PreApprovalClient $client,
    ) {}

    public function createPendingPreapproval(
        Plan $plan,
        Negocio $negocio,
        string $payerEmail,
    ): Suscripcion {
        try {
            $created = $this->client->create([
                'reason' => $plan->nombre,
                'external_reference' => (string) $negocio->id,
                'payer_email' => $payerEmail,
                'auto_recurring' => [
                    'frequency' => (int) config('mercadopago.frequency', 1),
                    'frequency_type' => config('mercadopago.frequency_type', 'months'),
                    'repetitions' => (int) config('mercadopago.repetitions', 12),
                    'transaction_amount' => (float) $plan->precio,
                    'currency_id' => config('mercadopago.currency', 'ARS'),
                ],
                'back_url' => rtrim((string) config('app.url'), '/').route('planes.retorno', [], false),
                'status' => 'pending',
            ]);
        } catch (MPApiException $e) {
            throw MercadoPagoException::fromApi($e);
        }

        $content = $created->getResponse()->getContent();

        $preapprovalId = $content['id'] ?? $created->id;
        $mpStatus = $content['status'] ?? $created->status;
        $start = $content['date_created'] ?? Carbon::now();

        $suscripcion = $negocio->suscripcion ?? new Suscripcion(['negocios_id' => $negocio->id]);

        $suscripcion->forceFill([
            'plan_id' => $plan->id,
            'tipo' => 'mercadopago',
            'estado' => false,
            'mp_preapproval_id' => $preapprovalId,
            'mp_status' => $mpStatus,
            'inicio' => $start,
            'metadatos' => $content,
        ])->save();

        return $suscripcion->fresh();
    }

    public function cancel(Suscripcion $suscripcion): void
    {
        if ($suscripcion->mp_preapproval_id) {
            try {
                $this->client->update($suscripcion->mp_preapproval_id, ['status' => 'cancelled']);
            } catch (MPApiException $e) {
                throw MercadoPagoException::fromApi($e);
            }
        }

        $suscripcion->forceFill([
            'estado' => false,
            'mp_status' => 'cancelled',
        ])->save();
    }

    public function changeCard(Suscripcion $suscripcion): string
    {
        if (! $suscripcion->mp_preapproval_id) {
            throw new MercadoPagoException('La suscripción no tiene una suscripción de Mercado Pago asociada.');
        }

        try {
            $preapproval = $this->client->get($suscripcion->mp_preapproval_id);
        } catch (MPApiException $e) {
            throw MercadoPagoException::fromApi($e);
        }

        $content = $preapproval->getResponse()->getContent();

        $initPoint = $content['init_point'] ?? $preapproval->init_point;

        if (! $initPoint) {
            throw new MercadoPagoException('Mercado Pago no devolvió una URL para actualizar el medio de pago.');
        }

        return $initPoint;
    }

    public function syncFromPreapproval(string $preapprovalId): ?Suscripcion
    {
        $suscripcion = Suscripcion::query()
            ->where('mp_preapproval_id', $preapprovalId)
            ->first();

        if (! $suscripcion) {
            return null;
        }

        try {
            $preapproval = $this->client->get($preapprovalId);
        } catch (MPApiException $e) {
            throw MercadoPagoException::fromApi($e);
        }

        $content = $preapproval->getResponse()->getContent();

        $suscripcion->forceFill([
            'estado' => $preapproval->status === 'authorized',
            'mp_status' => $preapproval->status,
            'fin' => $content['auto_recurring']['end_date'] ?? $content['next_payment_date'] ?? $suscripcion->fin,
            'proxima_facturacion' => $content['next_payment_date'] ?? $suscripcion->proxima_facturacion,
            'metadatos' => $content,
        ])->save();

        return $suscripcion;
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    public function markPaymentApproved(string $preapprovalId, array $payment): ?Suscripcion
    {
        $suscripcion = Suscripcion::query()
            ->where('mp_preapproval_id', $preapprovalId)
            ->first();

        if (! $suscripcion) {
            return null;
        }

        $suscripcion->forceFill([
            'estado' => true,
            'mp_status' => 'authorized',
            'ultimo_pago' => $payment['date_approved'] ?? Carbon::now(),
            'proxima_facturacion' => $payment['next_payment_date'] ?? $suscripcion->proxima_facturacion,
        ])->save();

        return $suscripcion;
    }
}
