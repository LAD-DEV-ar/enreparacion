<?php

namespace Tests\Feature\Planes;

use App\Models\Plan;
use App\Models\User;
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
        $response->assertSee('Plan');
        $response->assertSee('Suscribirse');
    }
}
