<?php

namespace Tests\Feature\MercadoPago;

use App\Exceptions\MercadoPagoException;
use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
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

    public function test_it_cancels_the_subscription_in_mercado_pago_and_keeps_access_until_the_end_of_the_period(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPResponse(200, ['id' => 'preapproval-999', 'status' => 'cancelled']));
        $this->bindFakeHttp($http);

        $suscripcion = Suscripcion::factory()->create([
            'mp_preapproval_id' => 'preapproval-999',
            'mp_status' => 'authorized',
            'estado' => true,
            'proxima_facturacion' => now()->addMonth(),
        ]);

        app(SubscriptionService::class)->cancel($suscripcion);

        $this->assertCount(1, $http->requests);
        $this->assertSame(
            '{"status":"cancelled"}',
            $http->requests[0]->getPayload(),
        );

        $this->assertDatabaseHas('suscripciones', [
            'id' => $suscripcion->id,
            'mp_status' => 'cancelled',
            'estado' => true,
            'proxima_facturacion' => null,
        ]);
    }

    public function test_it_wraps_api_errors_when_cancelling(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPApiException('Bad Request', new MPResponse(400, ['message' => 'invalid subscription'])));
        $this->bindFakeHttp($http);

        $suscripcion = Suscripcion::factory()->create(['mp_preapproval_id' => 'preapproval-999']);

        $this->expectException(MercadoPagoException::class);

        app(SubscriptionService::class)->cancel($suscripcion);
    }

    public function test_change_card_returns_the_init_point_from_mercado_pago(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPResponse(200, [
            'id' => 'preapproval-999',
            'status' => 'authorized',
            'init_point' => 'https://www.mercadopago.com.ar/subscriptions/change_card?preapproval_id=preapproval-999',
        ]));
        $this->bindFakeHttp($http);

        $suscripcion = Suscripcion::factory()->create([
            'mp_preapproval_id' => 'preapproval-999',
            'mp_status' => 'authorized',
        ]);

        $initPoint = app(SubscriptionService::class)->changeCard($suscripcion);

        $this->assertSame(
            'https://www.mercadopago.com.ar/subscriptions/change_card?preapproval_id=preapproval-999',
            $initPoint,
        );
    }

    public function test_change_card_requires_an_active_subscription(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $this->bindFakeHttp($http);

        $suscripcion = Suscripcion::factory()->pendiente()->create(['mp_preapproval_id' => 'preapproval-999']);

        $this->expectException(MercadoPagoException::class);

        app(SubscriptionService::class)->changeCard($suscripcion);

        $this->assertCount(0, $http->requests);
    }

    public function test_it_uses_the_request_host_for_the_back_url_when_it_is_a_public_url(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $this->fakePendingResponse($http);
        $this->bindFakeHttp($http);

        $request = Request::create('https://app.trycloudflare.com/planes', 'POST', server: ['HTTP_HOST' => 'app.trycloudflare.com']);
        $this->app->instance('request', $request);

        $plan = Plan::factory()->create();
        $negocio = Negocio::factory()->create();

        app(SubscriptionService::class)->createPendingPreapproval(
            plan: $plan,
            negocio: $negocio,
            payerEmail: 'cliente@example.com',
        );

        $payload = json_decode($http->requests[0]->getPayload(), true);

        $this->assertSame('https://app.trycloudflare.com/planes/retorno', $payload['back_url']);
    }

    public function test_it_falls_back_to_app_url_for_the_back_url_when_the_host_is_local(): void
    {
        config(['app.url' => 'https://app.trycloudflare.com']);

        $http = new FakeMercadoPagoHttpClient;
        $this->fakePendingResponse($http);
        $this->bindFakeHttp($http);

        $request = Request::create('http://localhost/planes', 'POST', server: ['HTTP_HOST' => 'localhost']);
        $this->app->instance('request', $request);

        $plan = Plan::factory()->create();
        $negocio = Negocio::factory()->create();

        app(SubscriptionService::class)->createPendingPreapproval(
            plan: $plan,
            negocio: $negocio,
            payerEmail: 'cliente@example.com',
        );

        $payload = json_decode($http->requests[0]->getPayload(), true);

        $this->assertSame('https://app.trycloudflare.com/planes/retorno', $payload['back_url']);
    }
}
