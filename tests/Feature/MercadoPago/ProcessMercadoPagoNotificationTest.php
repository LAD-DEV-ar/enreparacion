<?php

namespace Tests\Feature\MercadoPago;

use App\Jobs\ProcessMercadoPagoNotification;
use App\Models\Suscripcion;
use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Net\MPResponse;
use Tests\Support\FakeMercadoPagoHttpClient;
use Tests\TestCase;

class ProcessMercadoPagoNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function bindFakeHttp(FakeMercadoPagoHttpClient $http): void
    {
        $this->app->instance(PreApprovalClient::class, new PreApprovalClient($http));
        $this->app->instance(PaymentClient::class, new PaymentClient($http));
    }

    public function test_it_authorizes_the_subscription_on_a_subscription_preapproval_notification(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPResponse(200, [
            'id' => 'preapproval-999',
            'status' => 'authorized',
            'next_payment_date' => '2026-02-01T00:00:00.000-03:00',
            'auto_recurring' => ['end_date' => '2026-12-01T00:00:00.000-03:00'],
        ]));
        $this->bindFakeHttp($http);

        $suscripcion = $this->makePendingSubscription('preapproval-999');

        (new ProcessMercadoPagoNotification('subscription_preapproval', 'preapproval-999'))
            ->handle($this->app->make(SubscriptionService::class), $this->app->make(PaymentClient::class));

        $suscripcion->refresh();

        $this->assertTrue($suscripcion->estado);
        $this->assertSame('authorized', $suscripcion->mp_status);
        $this->assertNotNull($suscripcion->proxima_facturacion);
    }

    public function test_it_marks_the_subscription_approved_on_a_subscription_authorized_payment_notification(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPResponse(200, [
            'id' => 7032205970,
            'status' => 'approved',
            'preapproval_id' => 'preapproval-999',
            'date_approved' => '2026-01-15T00:00:00.000-03:00',
            'next_payment_date' => '2026-02-01T00:00:00.000-03:00',
        ]));
        $this->bindFakeHttp($http);

        $suscripcion = $this->makePendingSubscription('preapproval-999');

        (new ProcessMercadoPagoNotification('subscription_authorized_payment', '7032205970'))
            ->handle($this->app->make(SubscriptionService::class), $this->app->make(PaymentClient::class));

        $suscripcion->refresh();

        $this->assertTrue($suscripcion->estado);
        $this->assertSame('authorized', $suscripcion->mp_status);
        $this->assertNotNull($suscripcion->ultimo_pago);
        $this->assertNotNull($suscripcion->proxima_facturacion);
    }

    public function test_it_still_handles_the_legacy_preapproval_topic(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $http->push(new MPResponse(200, [
            'id' => 'preapproval-999',
            'status' => 'authorized',
            'next_payment_date' => '2026-02-01T00:00:00.000-03:00',
        ]));
        $this->bindFakeHttp($http);

        $suscripcion = $this->makePendingSubscription('preapproval-999');

        (new ProcessMercadoPagoNotification('preapproval', 'preapproval-999'))
            ->handle($this->app->make(SubscriptionService::class), $this->app->make(PaymentClient::class));

        $suscripcion->refresh();

        $this->assertTrue($suscripcion->estado);
        $this->assertSame('authorized', $suscripcion->mp_status);
    }

    public function test_it_ignores_other_topics(): void
    {
        $http = new FakeMercadoPagoHttpClient;
        $this->bindFakeHttp($http);

        $suscripcion = $this->makePendingSubscription('preapproval-999');

        (new ProcessMercadoPagoNotification('something_else', 'x'))
            ->handle($this->app->make(SubscriptionService::class), $this->app->make(PaymentClient::class));

        $suscripcion->refresh();

        $this->assertFalse($suscripcion->estado);
        $this->assertSame('pending', $suscripcion->mp_status);
    }

    private function makePendingSubscription(string $preapprovalId)
    {
        return Suscripcion::factory()->pendiente()->create([
            'mp_preapproval_id' => $preapprovalId,
        ]);
    }
}
