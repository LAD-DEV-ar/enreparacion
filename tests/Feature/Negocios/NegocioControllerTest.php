<?php

namespace Tests\Feature\Negocios;

use App\Models\Negocio;
use App\Models\Plan;
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

    // ==================== Tests de Suscripción ====================

    public function test_guest_cannot_subscribe(): void
    {
        $plan = Plan::create([
            'nombre'      => 'Plan Inicial',
            'descripcion' => 'Primer mes gratis',
            'precio'      => '0',
            'activo'      => true,
        ]);

        $negocio = Negocio::create([
            'nombre' => 'Reparaciones Express',
        ]);

        $response = $this->post(route('negocios.suscribir'), [
            'negocios_id' => $negocio->id,
            'plan_id'     => $plan->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_subscribe_to_a_plan(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $negocio = Negocio::create([
            'nombre' => 'Reparaciones Express',
        ]);

        $user->negocios_id = $negocio->id;
        $user->save();

        $plan = Plan::create([
            'nombre'      => 'Plan Inicial',
            'descripcion' => 'Primer mes gratis',
            'precio'      => '0',
            'activo'      => true,
        ]);

        $response = $this->actingAs($user)->post(route('negocios.suscribir'), [
            'negocios_id' => $negocio->id,
            'plan_id'     => $plan->id,
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('suscripciones', [
            'negocios_id' => $negocio->id,
            'plan_id'     => $plan->id,
            'estado'      => true,
        ]);

        $suscripcion = \App\Models\Suscripcion::where('negocios_id', $negocio->id)->first();
        $this->assertNotNull($suscripcion);
        $this->assertTrue($suscripcion->estado);
        $this->assertNotNull($suscripcion->inicio);
        $this->assertNotNull($suscripcion->fin);
        $this->assertNotNull($suscripcion->ultimo_pago);
        $this->assertNotNull($suscripcion->proxima_facturacion);
    }

    public function test_subscribe_fails_when_negocios_id_is_missing(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $plan = Plan::create([
            'nombre'      => 'Plan Inicial',
            'descripcion' => 'Primer mes gratis',
            'precio'      => '0',
            'activo'      => true,
        ]);

        $response = $this->actingAs($user)->post(route('negocios.suscribir'), [
            'plan_id' => $plan->id,
        ]);

        $response->assertSessionHasErrors(['negocios_id']);
        $this->assertDatabaseCount('suscripciones', 0);
    }

    public function test_subscribe_fails_when_plan_id_is_missing(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $negocio = Negocio::create([
            'nombre' => 'Reparaciones Express',
        ]);

        $response = $this->actingAs($user)->post(route('negocios.suscribir'), [
            'negocios_id' => $negocio->id,
        ]);

        $response->assertSessionHasErrors(['plan_id']);
        $this->assertDatabaseCount('suscripciones', 0);
    }

    public function test_subscribe_fails_with_invalid_negocio_id(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $plan = Plan::create([
            'nombre'      => 'Plan Inicial',
            'descripcion' => 'Primer mes gratis',
            'precio'      => '0',
            'activo'      => true,
        ]);

        $response = $this->actingAs($user)->post(route('negocios.suscribir'), [
            'negocios_id' => 9999,
            'plan_id'     => $plan->id,
        ]);

        $response->assertSessionHasErrors(['negocios_id']);
        $this->assertDatabaseCount('suscripciones', 0);
    }

    public function test_subscribe_fails_with_invalid_plan_id(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $negocio = Negocio::create([
            'nombre' => 'Reparaciones Express',
        ]);

        $response = $this->actingAs($user)->post(route('negocios.suscribir'), [
            'negocios_id' => $negocio->id,
            'plan_id'     => 9999,
        ]);

        $response->assertSessionHasErrors(['plan_id']);
        $this->assertDatabaseCount('suscripciones', 0);
    }
}
