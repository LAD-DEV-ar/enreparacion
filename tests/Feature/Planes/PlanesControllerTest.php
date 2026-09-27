<?php

namespace Tests\Feature\Planes;

use App\Exceptions\MercadoPagoException;
use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use App\Services\MercadoPago\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_planes_screen(): void
    {
        $response = $this->get(route('planes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_user_cannot_access_planes_screen_and_is_redirected_to_verify_email(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('planes.index'));

        $response->assertRedirect(route('verificar-email.index'));
    }

    public function test_authenticated_and_verified_user_can_view_planes_screen_without_plans(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('planes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.planes');
        $response->assertSee('No hay planes');
        $response->assertSee('Actualmente no hay planes');
    }

    public function test_authenticated_and_verified_user_can_view_planes_screen(): void
    {
        $user = User::factory()->create();
        Plan::factory()->create();

        $response = $this->actingAs($user)->get(route('planes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.planes');
        $response->assertSee('Plan Inicial');
        $response->assertSee('Suscribirme con Mercado Pago');
    }

    public function test_user_is_redirected_to_mercado_pago_when_starting_a_subscription(): void
    {
        $user = User::factory()->create();
        $negocio = Negocio::factory()->create();
        $user->update(['negocios_id' => $negocio->id]);
        $plan = Plan::factory()->create();

        $initPoint = 'https://www.mercadopago.com.ar/subscriptions/checkout?preapproval_id=preapproval-999';

        $this->mock(SubscriptionService::class, function ($mock) use ($initPoint): void {
            $mock->shouldReceive('createPendingPreapproval')
                ->once()
                ->andReturn(new Suscripcion(['metadatos' => ['init_point' => $initPoint]]));
        });

        $response = $this->actingAs($user)->post(route('planes.store'), [
            'plan' => $plan->id,
        ]);

        $response->assertRedirect($initPoint);
    }

    public function test_subscribe_requires_a_plan(): void
    {
        $user = User::factory()->create();
        $negocio = Negocio::factory()->create();
        $user->update(['negocios_id' => $negocio->id]);

        $response = $this->actingAs($user)->postJson(route('planes.store'), []);

        $response->assertJsonValidationErrors(['plan']);
    }

    public function test_subscribe_returns_an_error_when_mercado_pago_fails(): void
    {
        $user = User::factory()->create();
        $negocio = Negocio::factory()->create();
        $user->update(['negocios_id' => $negocio->id]);
        $plan = Plan::factory()->create();

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('createPendingPreapproval')
                ->once()
                ->andThrow(new MercadoPagoException('Error de Mercado Pago'));
        });

        $response = $this->actingAs($user)->postJson(route('planes.store'), [
            'plan' => $plan->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['error']);
    }

    public function test_subscribe_fails_without_a_negocio(): void
    {
        $user = User::factory()->create(['negocios_id' => null]);
        $plan = Plan::factory()->create();

        $response = $this->actingAs($user)->postJson(route('planes.store'), [
            'plan' => $plan->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_retorno_syncs_the_subscription_and_redirects_to_dashboard(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('syncFromPreapproval')
                ->once()
                ->andReturn(new Suscripcion(['estado' => true]));
        });

        $response = $this->actingAs($user)->get(route('planes.retorno', [
            'preapproval_id' => 'preapproval-999',
        ]));

        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_retorno_redirects_to_planes_when_payment_is_not_confirmed(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('syncFromPreapproval')
                ->once()
                ->andReturn(new Suscripcion(['estado' => false]));
        });

        $response = $this->actingAs($user)->get(route('planes.retorno', [
            'preapproval_id' => 'preapproval-999',
        ]));

        $response->assertRedirect(route('planes.index'));
    }

    public function test_user_can_cancel_an_active_subscription(): void
    {
        $negocio = Negocio::factory()->conSuscripcion()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('cancel')->once();
        });

        $response = $this->actingAs($user)->from(route('cuenta.index'))->post(route('planes.cancelar'));

        $response->assertRedirect(route('cuenta.index'));
        $response->assertSessionHas('success');
    }

    public function test_user_cannot_cancel_an_inactive_or_cancelled_subscription(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        Suscripcion::factory()->cancelada()->create(['negocios_id' => $negocio->id]);

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('cancel')->never();
        });

        $response = $this->actingAs($user)->from(route('cuenta.index'))->post(route('planes.cancelar'));

        $response->assertRedirect(route('cuenta.index'));
        $response->assertSessionHas('info');
    }

    public function test_cancelation_requires_an_active_subscription(): void
    {
        $user = User::factory()->create(['negocios_id' => null]);

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('cancel')->never();
        });

        $response = $this->actingAs($user)->from(route('cuenta.index'))->post(route('planes.cancelar'));

        $response->assertRedirect(route('cuenta.index'));
        $response->assertSessionHas('info');
    }

    public function test_change_card_redirects_to_mercado_pago(): void
    {
        $negocio = Negocio::factory()->conSuscripcion()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $initPoint = 'https://www.mercadopago.com.ar/subscriptions/change_card?preapproval_id=preapproval-999';

        $this->mock(SubscriptionService::class, function ($mock) use ($initPoint): void {
            $mock->shouldReceive('changeCard')->once()->andReturn($initPoint);
        });

        $response = $this->actingAs($user)->post(route('planes.tarjeta'));

        $response->assertRedirect($initPoint);
    }

    public function test_change_card_requires_an_active_subscription(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        Suscripcion::factory()->cancelada()->create(['negocios_id' => $negocio->id]);

        $this->mock(SubscriptionService::class, function ($mock): void {
            $mock->shouldReceive('changeCard')->never();
        });

        $response = $this->actingAs($user)->from(route('cuenta.index'))->post(route('planes.tarjeta'));

        $response->assertRedirect(route('cuenta.index'));
        $response->assertSessionHas('error');
    }
}
