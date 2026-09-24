<?php

namespace Tests\Feature\Negocios;

use App\Models\Negocio;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegocioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_negocio_registration_screen(): void
    {
        $response = $this->get(route('negocios'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_without_negocio_can_view_registration_screen(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('negocios'));

        $response->assertStatus(200);
        $response->assertViewIs('negocios.registro-negocios');
        $response->assertSee('Registra tu Negocio');
    }

    public function test_authenticated_user_with_existing_negocio_is_redirected_to_dashboard(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Electro Fix',
            'direccion' => 'Av. Corrientes 1000',
            'telefono' => '1144332211',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->get(route('negocios'));

        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_user_can_register_a_negocio(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('negocios.store'), [
            'nombre' => 'Reparaciones Express',
            'direccion' => 'Av. Rivadavia 5000',
            'telefono' => '1198765432',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['negocio_id']);

        $this->assertDatabaseHas('negocios', [
            'nombre' => 'Reparaciones Express',
            'direccion' => 'Av. Rivadavia 5000',
            'telefono' => '1198765432',
        ]);

        $negocio = Negocio::where('nombre', 'Reparaciones Express')->first();
        $this->assertNotNull($negocio);

        $user->refresh();
        $this->assertEquals($negocio->id, $user->negocios_id);
    }

    public function test_negocio_registration_fails_when_nombre_is_missing(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('negocios.store'), [
            'direccion' => 'Av. Rivadavia 5000',
            'telefono' => '1198765432',
        ]);

        $response->assertJsonValidationErrors(['nombre']);
        $this->assertDatabaseCount('negocios', 0);
    }

    public function test_user_with_existing_negocio_cannot_register_another(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Electro Fix',
            'direccion' => 'Av. Corrientes 1000',
            'telefono' => '1144332211',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('negocios.store'), [
            'nombre' => 'Otro Negocio',
        ]);

        $response->assertStatus(403);
    }

    // ==================== Suscripción de prueba ====================

    public function test_registering_a_negocio_creates_a_trial_subscription(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson(route('negocios.store'), [
            'nombre' => 'Reparaciones Express',
        ]);

        $response->assertOk();

        $negocio = Negocio::where('nombre', 'Reparaciones Express')->firstOrFail();

        $this->assertDatabaseHas('suscripciones', [
            'negocios_id' => $negocio->id,
            'tipo' => 'trial',
            'estado' => true,
        ]);

        $suscripcion = Suscripcion::where('negocios_id', $negocio->id)->firstOrFail();

        $this->assertNull($suscripcion->plan_id);
        $this->assertNull($suscripcion->ultimo_pago);
        $this->assertSame(
            now()->addDays((int) config('mercadopago.trial_days', 30))->toDateString(),
            $suscripcion->fin->toDateString(),
        );
    }
}
