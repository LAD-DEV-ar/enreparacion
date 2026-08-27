<?php

namespace Tests\Feature\Clientes;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_clientes_page(): void
    {
        $response = $this->get(route('clientes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_is_redirected_from_clientes_page(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('clientes.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_with_negocio_can_view_clientes_page(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $cliente = Cliente::factory()->paraNegocio($negocio)->create([
            'nombre' => 'María Gomez',
            'telefono' => '1122334455',
            'email' => 'maria@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('clientes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.clientes');
        $response->assertViewHas('clientes');
        $response->assertViewHas('totalClientes', 1);
        $response->assertSee('María Gomez');
    }

    public function test_clientes_page_only_shows_clients_belonging_to_authenticated_users_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();
        $clienteA = Cliente::factory()->paraNegocio($negocioA)->create([
            'nombre' => 'Cliente Negocio A',
        ]);

        $negocioB = Negocio::factory()->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create([
            'nombre' => 'Cliente Negocio B',
        ]);

        $response = $this->actingAs($userA)->get(route('clientes.index'));

        $response->assertStatus(200);
        $clientesEnVista = $response->viewData('clientes');

        $this->assertCount(1, $clientesEnVista);
        $this->assertEquals($clienteA->id, $clientesEnVista->first()['id']);
        $response->assertSee('Cliente Negocio A');
        $response->assertDontSee('Cliente Negocio B');
    }

    public function test_clientes_page_computes_counters_and_details_correctly(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $cliente = Cliente::factory()->paraNegocio($negocio)->create([
            'nombre' => 'Carlos Rodriguez',
        ]);

        $dispositivo1 = Dispositivo::factory()->paraCliente($cliente)->create([
            'marca_y_modelo' => 'iPhone 13',
        ]);
        $dispositivo2 = Dispositivo::factory()->paraCliente($cliente)->create([
            'marca_y_modelo' => 'Samsung Galaxy S23',
        ]);

        // Reparación 1: En taller ('en_reparacion')
        Reparacion::factory()->paraContexto($negocio, $dispositivo1, $user)->create([
            'estado' => 'en_reparacion',
            'costo_estimado' => 30000,
            'sena' => 10000,
        ]);

        // Reparación 2: Finalizada / entregada
        Reparacion::factory()->paraContexto($negocio, $dispositivo2, $user)->create([
            'estado' => 'entregado',
            'costo_estimado' => 20000,
            'sena' => 20000,
        ]);

        $response = $this->actingAs($user)->get(route('clientes.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalClientes', 1);
        $response->assertViewHas('totalEquiposEnTaller', 1);
        $response->assertViewHas('totalReparacionesNegocio', 2);

        $clientesData = $response->viewData('clientes');
        $clienteData = $clientesData->first();

        $this->assertEquals(2, $clienteData['total_reparaciones']);
        $this->assertEquals(1, $clienteData['equipos_en_taller']);
        $this->assertCount(2, $clienteData['dispositivos']);
        $this->assertCount(2, $clienteData['historial_reparaciones']);
    }

    public function test_guest_cannot_store_cliente(): void
    {
        $response = $this->post(route('clientes.store'), [
            'nombre' => 'Juan Perez',
            'telefono' => '1199887766',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_cannot_store_cliente(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Juan Perez',
            'telefono' => '1199887766',
        ]);

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_can_create_a_new_cliente(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $payload = [
            'nombre' => 'Laura Pausini',
            'telefono' => '1144556677',
            'email' => 'laura@example.com',
        ];

        $response = $this->actingAs($user)->post(route('clientes.store'), $payload);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success', 'Cliente registrado correctamente.');

        $this->assertDatabaseHas('clientes', [
            'negocios_id' => $negocio->id,
            'nombre' => 'Laura Pausini',
            'telefono' => '1144556677',
            'email' => 'laura@example.com',
        ]);
    }

    public function test_user_can_create_cliente_without_email(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $payload = [
            'nombre' => 'Marcos Rojo',
            'telefono' => '1166778899',
            'email' => null,
        ];

        $response = $this->actingAs($user)->post(route('clientes.store'), $payload);

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('clientes', [
            'negocios_id' => $negocio->id,
            'nombre' => 'Marcos Rojo',
            'telefono' => '1166778899',
            'email' => null,
        ]);
    }

    public function test_store_cliente_validation_fails_when_required_fields_are_missing(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->post(route('clientes.store'), []);

        $response->assertSessionHasErrors(['nombre', 'telefono']);
        $this->assertDatabaseCount('clientes', 0);
    }

    public function test_store_cliente_validation_fails_with_invalid_email(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->post(route('clientes.store'), [
            'nombre' => 'Valeria Lynch',
            'telefono' => '1122334455',
            'email' => 'esto-no-es-un-email',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('clientes', 0);
    }
}
