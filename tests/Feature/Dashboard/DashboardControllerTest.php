<?php

namespace Tests\Feature\Dashboard;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_is_redirected_to_negocio_registration(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_with_negocio_can_view_dashboard(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Servicio Técnico Pro',
            'direccion' => 'Belgrano 450',
            'telefono' => '1155443322',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_reparacion_with_cliente_and_dispositivo(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Servicio Técnico Pro',
            'direccion' => 'Belgrano 450',
            'telefono' => '1155443322',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $payload = [
            'nombre' => 'Juan Pérez',
            'telefono' => '1199887766',
            'email' => 'juanperez@example.com',
            'falla_reportada' => 'No enciende la pantalla',
            'sena' => 15000,
            'marca_y_modelo' => 'Samsung Galaxy S22',
            'clave_de_acceso' => '1234',
            'imei_o_serie' => '354896123456789',
            'costo_estimado' => 45000,
        ];

        $response = $this->actingAs($user)->post(route('dashboard.store'), $payload);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clientes', [
            'negocios_id' => $negocio->id,
            'nombre' => 'Juan Pérez',
            'telefono' => '1199887766',
            'email' => 'juanperez@example.com',
        ]);

        $this->assertDatabaseHas('dispositivos', [
            'marca_y_modelo' => 'Samsung Galaxy S22',
            'imei_o_serie' => '354896123456789',
        ]);

        $this->assertDatabaseHas('reparaciones', [
            'negocios_id' => $negocio->id,
            'users_id' => $user->id,
            'falla_reportada' => 'No enciende la pantalla',
            'sena' => 15000,
            'costo_estimado' => 45000,
            'clave_de_acceso' => '1234',
        ]);
    }

    public function test_reparacion_creation_fails_when_required_fields_are_missing(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Servicio Técnico Pro',
            'direccion' => 'Belgrano 450',
            'telefono' => '1155443322',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.store'), []);

        $response->assertSessionHasErrors([
            'nombre',
            'telefono',
            'falla_reportada',
            'marca_y_modelo',
        ]);

        $this->assertDatabaseCount('clientes', 0);
        $this->assertDatabaseCount('dispositivos', 0);
        $this->assertDatabaseCount('reparaciones', 0);
    }

    public function test_reparacion_creation_fails_when_numeric_fields_are_invalid(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Servicio Técnico Pro',
            'direccion' => 'Belgrano 450',
            'telefono' => '1155443322',
        ]);

        $user = User::factory()->create([
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->post(route('dashboard.store'), [
            'nombre' => 'Juan Pérez',
            'telefono' => '1199887766',
            'falla_reportada' => 'No enciende',
            'marca_y_modelo' => 'Motorola G54',
            'sena' => 'no-es-un-numero',
            'costo_estimado' => 'tampoco-un-numero',
        ]);

        $response->assertSessionHasErrors(['sena', 'costo_estimado']);
    }
}
