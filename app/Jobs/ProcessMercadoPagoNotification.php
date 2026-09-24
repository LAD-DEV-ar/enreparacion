<?php

namespace App\Jobs;

use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use MercadoPago\Client\Payment\PaymentClient;

class ProcessMercadoPagoNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $topic,
        public string $resource,
    ) {}

    public function handle(SubscriptionService $subscriptions, PaymentClient $payments): void
    {
        if ($this->topic === 'preapproval') {
            $subscriptions->syncFromPreapproval($this->resource);

            return;
        }

        if ($this->topic === 'authorized_payment' || $this->topic === 'payment') {
            try {
                $payment = $payments->get((int) $this->resource);
            } catch (\Throwable $e) {
                return;
            }

            $content = $payment->getResponse()->getContent();
            $preapprovalId = $content['preapproval_id'] ?? null;

            if (! $preapprovalId) {
                return;
            }

            if (($content['status'] ?? null) === 'approved') {
                $subscriptions->markPaymentApproved((string) $preapprovalId, $content);
            }
        }
    }
}
