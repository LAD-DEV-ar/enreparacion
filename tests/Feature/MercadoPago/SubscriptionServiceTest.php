<?php

namespace Tests\Feature\MercadoPago;

use App\Exceptions\MercadoPagoException;
use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Net\MPResponse;
use Tests\Support\FakeMercadoPagoHttpClient;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    private function bindFakeHttp(FakeMercadoPagoHttpClient $http): void
    {
        $this->app->instance(PreApprovalClient::class, new PreApprovalClient($http));
    }

    private function fakePendingResponse(FakeMercadoPagoHttpClient $http): void
    {
        $http->push(new MPResponse(201, [
            'id' => 'preapproval-999',
            'status' => 'pending',
            'external_reference' => '1',
            'init_point' => 'https://www.mercadopago.com.ar/subscriptions/checkout?preapproval_id=preapproval-999',
            'date_created' => '2026-01-01T00:00:00.000-03:00',
        ]));
    }

    public function test_it_creates_a_pending_subscription(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $this->fakePendingResponse($http);
        $this->bindFakeHttp($http);

        $plan = Plan::factory()->create();
        $negocio = Negocio::factory()->create();

        $suscripcion = app(SubscriptionService::class)->createPendingPreapproval(
            plan: $plan,
            negocio: $negocio,
            payerEmail: 'cliente@example.com',
        );

        $this->assertSame('mercadopago', $suscripcion->tipo);
        $this->assertFalse($suscripcion->estado);
        $this->assertSame('pending', $suscripcion->mp_status);
        $this->assertSame('preapproval-999', $suscripcion->mp_preapproval_id);
        $this->assertSame($plan->id, $suscripcion->plan_id);
        $this->assertSame(
            'https://www.mercadopago.com.ar/subscriptions/checkout?preapproval_id=preapproval-999',
            $suscripcion->metadatos['init_point'],
        );

        $this->assertCount(1, $http->requests);
    }

    public function test_it_updates_the_existing_trial_subscription_instead_of_creating_a_new_one(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $this->fakePendingResponse($http);
        $this->bindFakeHttp($http);

        $plan = Plan::factory()->create();
        $negocio = Negocio::factory()->create();
        $trial = Suscripcion::factory()->trial()->create([
            'negocios_id' => $negocio->id,
            'plan_id' => $plan->id,
        ]);

        $suscripcion = app(SubscriptionService::class)->createPendingPreapproval(
            plan: $plan,
            negocio: $negocio,
            payerEmail: 'cliente@example.com',
        );

        $this->assertSame($trial->id, $suscripcion->id);
        $this->assertDatabaseCount('suscripciones', 1);
        $this->assertSame('mercadopago', $suscripcion->tipo);
    }

    public function test_it_wraps_api_errors_in_a_mercado_pago_exception(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPApiException('Bad Request', new MPResponse(400, ['message' => 'invalid subscription'])));
        $this->bindFakeHttp($http);

        $plan = Plan::factory()->create();
        $negocio = Negocio::factory()->create();

        $this->expectException(MercadoPagoException::class);

        app(SubscriptionService::class)->createPendingPreapproval(
            plan: $plan,
            negocio: $negocio,
            payerEmail: 'cliente@example.com',
        );
    }
}
