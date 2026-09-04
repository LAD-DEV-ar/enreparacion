<?php

namespace Tests\Feature\Clientes;

use App\Models\Cliente;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteEdicionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->patchJson(route('clientes.update', $cliente), [
            'nombre' => 'Nuevo Nombre',
            'telefono' => '1122334455',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_cliente_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create([
            'nombre' => 'Carlos López',
            'telefono' => '1100000000',
            'email' => 'carlos@old.com',
        ]);

        $response = $this->actingAs($user)->patchJson(route('clientes.update', $cliente), [
            'nombre' => 'Carlos M. López',
            'telefono' => '1199998888',
            'email' => 'carlos@new.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Datos del cliente actualizados correctamente.',
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => 'Carlos M. López',
                'telefono' => '1199998888',
                'email' => 'carlos@new.com',
                'iniciales' => 'CL',
            ],
        ]);

        $cliente->refresh();
        $this->assertEquals('Carlos M. López', $cliente->nombre);
        $this->assertEquals('1199998888', $cliente->telefono);
        $this->assertEquals('carlos@new.com', $cliente->email);
    }

    public function test_user_cannot_update_cliente_of_another_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();

        $negocioB = Negocio::factory()->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create([
            'nombre' => 'Cliente Negocio B',
        ]);

        $response = $this->actingAs($userA)->patchJson(route('clientes.update', $clienteB), [
            'nombre' => 'Intento Hack',
            'telefono' => '1111111111',
        ]);

        $response->assertStatus(403);
        $clienteB->refresh();
        $this->assertEquals('Cliente Negocio B', $clienteB->nombre);
    }

    public function test_update_cliente_validates_required_fields(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();

        $response = $this->actingAs($user)->patchJson(route('clientes.update', $cliente), [
            'nombre' => '',
            'telefono' => '',
            'email' => 'email-invalido',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre', 'telefono', 'email']);
    }
}
