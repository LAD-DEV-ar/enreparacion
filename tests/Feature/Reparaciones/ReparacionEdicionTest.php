<?php

namespace Tests\Feature\Reparaciones;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReparacionEdicionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_reparacion(): void
    {
        $reparacion = Reparacion::factory()->create();

        $response = $this->patchJson(route('reparaciones.update', $reparacion), [
            'falla_reportada' => 'Cambio de pantalla',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_reparacion_diagnostico_and_financial_data(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'falla_reportada' => 'No enciende',
            'clave_de_acceso' => '1234',
            'costo_estimado' => 15000,
            'sena' => 5000,
        ]);

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update', $reparacion), [
            'falla_reportada' => 'Corto en línea principal, requiere microsoldadura',
            'clave_de_acceso' => 'Patrón en L',
            'costo_estimado' => 25000,
            'sena' => 10000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Reparación actualizada correctamente.',
            'reparacion' => [
                'id' => $reparacion->id,
                'falla_reportada' => 'Corto en línea principal, requiere microsoldadura',
                'clave_de_acceso' => 'Patrón en L',
                'costo_estimado' => '$25.000',
                'costo_estimado_num' => 25000,
                'sena' => '$10.000',
                'sena_num' => 10000,
                'saldo_pendiente' => '$15.000',
                'saldo_pendiente_num' => 15000,
                'esta_saldado' => false,
            ],
        ]);

        $reparacion->refresh();
        $this->assertEquals('Corto en línea principal, requiere microsoldadura', $reparacion->falla_reportada);
        $this->assertEquals('Patrón en L', $reparacion->clave_de_acceso);
        $this->assertEquals(25000, $reparacion->costo_estimado);
        $this->assertEquals(10000, $reparacion->sena);
    }

    public function test_user_can_mark_reparacion_as_fully_paid(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'costo_estimado' => 20000,
            'sena' => 5000,
        ]);

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update', $reparacion), [
            'costo_estimado' => 20000,
            'sena' => 20000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'reparacion' => [
                'saldo_pendiente' => '$0',
                'saldo_pendiente_num' => 0,
                'esta_saldado' => true,
            ],
        ]);
    }

    public function test_user_cannot_update_reparacion_of_another_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();

        $negocioB = Negocio::factory()->create();
        $userB = User::factory()->conNegocio($negocioB)->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create();
        $dispositivoB = Dispositivo::factory()->paraCliente($clienteB)->create();
        $reparacionB = Reparacion::factory()->paraContexto($negocioB, $dispositivoB, $userB)->create([
            'falla_reportada' => 'Falla original B',
        ]);

        $response = $this->actingAs($userA)->patchJson(route('reparaciones.update', $reparacionB), [
            'falla_reportada' => 'Intento Hack',
        ]);

        $response->assertStatus(403);
        $reparacionB->refresh();
        $this->assertEquals('Falla original B', $reparacionB->falla_reportada);
    }

    public function test_update_reparacion_validates_numeric_and_positive_amounts(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create();

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update', $reparacion), [
            'costo_estimado' => -500,
            'sena' => 'no-numerico',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['costo_estimado', 'sena']);
    }
}
