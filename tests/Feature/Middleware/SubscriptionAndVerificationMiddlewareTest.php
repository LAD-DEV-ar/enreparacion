<?php

namespace Tests\Feature\Middleware;

use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAndVerificationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_negocio_is_redirected_to_negocios_from_subscription_protected_route(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_with_negocio_but_no_subscription_is_redirected_to_planes(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertRedirect(route('planes.index'));
    }

    public function test_user_with_inactive_subscription_is_redirected_to_planes(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        Suscripcion::factory()->create([
            'negocios_id' => $negocio->id,
            'plan_id' => Plan::factory(),
            'estado' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertRedirect(route('planes.index'));
    }

    public function test_user_with_active_subscription_can_pass_subscription_middleware(): void
    {
        $negocio = Negocio::factory()->conSuscripcion()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertStatus(200);
    }

    public function test_unverified_email_user_is_redirected_to_verify_email(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('planes.index'));

        $response->assertRedirect(route('verificar-email.index'));
    }

    public function test_user_with_existing_negocio_is_redirected_away_from_tu_negocio_to_dashboard(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->get(route('negocios'));

        $response->assertRedirect(route('dashboard.index'));
    }
}
