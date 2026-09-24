<?php

namespace Tests\Feature\MercadoPago;

use App\Jobs\ProcessMercadoPagoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MercadoPagoWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_a_job_for_a_valid_notification(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/webhooks/mercado-pago', [
            'type' => 'preapproval',
            'data' => ['id' => 'preapproval-123'],
        ]);

        $response->assertOk();
        $response->assertJson(['received' => true]);

        Queue::assertPushed(ProcessMercadoPagoNotification::class, function ($job): bool {
            return $job->topic === 'preapproval' && $job->resource === 'preapproval-123';
        });
    }

    public function test_it_accepts_the_ipn_topic_format(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/webhooks/mercado-pago', [
            'topic' => 'authorized_payment',
            'id' => 'payment-123',
        ]);

        $response->assertOk();

        Queue::assertPushed(ProcessMercadoPagoNotification::class, function ($job): bool {
            return $job->topic === 'authorized_payment' && $job->resource === 'payment-123';
        });
    }

    public function test_it_rejects_payloads_without_topic_or_resource(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/webhooks/mercado-pago', []);

        $response->assertStatus(400);
        Queue::assertNothingPushed();
    }

    public function test_it_still_dispatches_when_the_signature_is_invalid(): void
    {
        Queue::fake();
        config(['mercadopago.webhook_secret' => 'test-secret']);

        $response = $this->postJson('/api/webhooks/mercado-pago', [
            'type' => 'preapproval',
            'data' => ['id' => 'preapproval-123'],
        ], [
            'x-signature' => 'ts=123,v1=invalid',
            'x-request-id' => 'request-1',
        ]);

        $response->assertOk();
        Queue::assertPushed(ProcessMercadoPagoNotification::class);
    }
}
