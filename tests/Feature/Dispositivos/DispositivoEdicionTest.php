<?php

namespace Tests\Feature\Dispositivos;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispositivoEdicionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_dispositivo(): void
    {
        $dispositivo = Dispositivo::factory()->create();

        $response = $this->patchJson(route('dispositivos.update', $dispositivo), [
            'marca_y_modelo' => 'Samsung S22',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_dispositivo_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create([
            'marca_y_modelo' => 'Motorola G20',
            'imei_o_serie' => '123456789012345',
        ]);

        $response = $this->actingAs($user)->patchJson(route('dispositivos.update', $dispositivo), [
            'marca_y_modelo' => 'Motorola G22 Pro',
            'imei_o_serie' => '987654321098765',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Datos del dispositivo actualizados correctamente.',
            'dispositivo' => [
                'id' => $dispositivo->id,
                'marca_y_modelo' => 'Motorola G22 Pro',
                'imei_o_serie' => '987654321098765',
            ],
        ]);

        $dispositivo->refresh();
        $this->assertEquals('Motorola G22 Pro', $dispositivo->marca_y_modelo);
        $this->assertEquals('987654321098765', $dispositivo->imei_o_serie);
    }

    public function test_user_cannot_update_dispositivo_of_another_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();

        $negocioB = Negocio::factory()->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create();
        $dispositivoB = Dispositivo::factory()->paraCliente($clienteB)->create([
            'marca_y_modelo' => 'iPhone 11',
        ]);

        $response = $this->actingAs($userA)->patchJson(route('dispositivos.update', $dispositivoB), [
            'marca_y_modelo' => 'iPhone 15 Pro',
        ]);

        $response->assertStatus(403);
        $dispositivoB->refresh();
        $this->assertEquals('iPhone 11', $dispositivoB->marca_y_modelo);
    }

    public function test_update_dispositivo_validates_required_fields(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();

        $response = $this->actingAs($user)->patchJson(route('dispositivos.update', $dispositivo), [
            'marca_y_modelo' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['marca_y_modelo']);
    }
}
