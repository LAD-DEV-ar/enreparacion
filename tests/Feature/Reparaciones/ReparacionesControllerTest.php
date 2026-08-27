<?php

namespace Tests\Feature\Reparaciones;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReparacionesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_reparaciones_page(): void
    {
        $response = $this->get(route('reparaciones.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_is_redirected_from_reparaciones_page(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('reparaciones.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_with_negocio_can_view_reparaciones_page(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'falla_reportada' => 'Cambio de pin de carga',
            'estado' => 'recibido',
        ]);

        $response = $this->actingAs($user)->get(route('reparaciones.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.reparaciones');
        $response->assertViewHas('reparaciones');
        $response->assertViewHas('totalTodas', 1);
        $response->assertSee('Cambio de pin de carga');
    }

    public function test_reparaciones_page_only_shows_reparaciones_belonging_to_authenticated_users_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();
        $clienteA = Cliente::factory()->paraNegocio($negocioA)->create();
        $dispositivoA = Dispositivo::factory()->paraCliente($clienteA)->create();
        $reparacionA = Reparacion::factory()->paraContexto($negocioA, $dispositivoA, $userA)->create([
            'falla_reportada' => 'Reparación del Negocio A',
        ]);

        $negocioB = Negocio::factory()->create();
        $userB = User::factory()->conNegocio($negocioB)->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create();
        $dispositivoB = Dispositivo::factory()->paraCliente($clienteB)->create();
        $reparacionB = Reparacion::factory()->paraContexto($negocioB, $dispositivoB, $userB)->create([
            'falla_reportada' => 'Reparación del Negocio B',
        ]);

        $response = $this->actingAs($userA)->get(route('reparaciones.index'));

        $response->assertStatus(200);
        $reparacionesEnVista = $response->viewData('reparaciones');

        $this->assertCount(1, $reparacionesEnVista);
        $this->assertEquals($reparacionA->id, $reparacionesEnVista->first()['id']);
        $response->assertSee('Reparación del Negocio A');
        $response->assertDontSee('Reparación del Negocio B');
    }

    public function test_reparaciones_page_calculates_state_counters_and_economic_totals(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();

        // 1 Recibida
        Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'recibido',
            'costo_estimado' => 20000,
            'sena' => 5000,
        ]);

        // 1 En reparación
        Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'en_reparacion',
            'costo_estimado' => 30000,
            'sena' => 10000,
        ]);

        // 1 Lista
        Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'listo',
            'costo_estimado' => 15000,
            'sena' => 15000,
        ]);

        // 1 Entregada
        Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'entregado',
            'costo_estimado' => 25000,
            'sena' => 25000,
        ]);

        // 1 Cancelada
        Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'cancelado',
            'costo_estimado' => 10000,
            'sena' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('reparaciones.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalTodas', 5);
        $response->assertViewHas('totalRecibidas', 1);
        $response->assertViewHas('totalEnReparacion', 1);
        $response->assertViewHas('totalListas', 1);
        $response->assertViewHas('totalEntregadas', 1);
        $response->assertViewHas('totalCanceladas', 1);

        // Total facturado = 20000 + 30000 + 15000 + 25000 + 10000 = 100000
        $response->assertViewHas('totalFacturado', 100000.0);
        // Total saldo pendiente = (20000-5000) + (30000-10000) + (15000-15000) + (25000-25000) + (10000-0) = 15000 + 20000 + 0 + 0 + 10000 = 45000
        $response->assertViewHas('totalPendienteCobro', 45000.0);
    }

    public function test_guest_cannot_store_reparacion(): void
    {
        $response = $this->post(route('reparaciones.store'), [
            'nombre' => 'Lucas Rossi',
            'telefono' => '1133445566',
            'marca_y_modelo' => 'Motorola Moto G84',
            'falla_reportada' => 'Batería inflada',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_cannot_store_reparacion(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->post(route('reparaciones.store'), [
            'nombre' => 'Lucas Rossi',
            'telefono' => '1133445566',
            'marca_y_modelo' => 'Motorola Moto G84',
            'falla_reportada' => 'Batería inflada',
        ]);

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_can_create_reparacion_with_cliente_and_dispositivo(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $payload = [
            'nombre' => 'Lucas Rossi',
            'telefono' => '1133445566',
            'email' => 'lucas@example.com',
            'marca_y_modelo' => 'Motorola Moto G84',
            'imei_o_serie' => '123456789012345',
            'clave_de_acceso' => '9876',
            'falla_reportada' => 'Batería se descarga rápido',
            'costo_estimado' => 45000,
            'sena' => 15000,
            'notas_internas' => 'El cliente necesita el equipo antes del viernes',
        ];

        $response = $this->actingAs($user)->post(route('reparaciones.store'), $payload);

        $response->assertRedirect(route('reparaciones.index'));
        $response->assertSessionHas('success', 'Reparación creada y registrada correctamente.');

        $this->assertDatabaseHas('clientes', [
            'negocios_id' => $negocio->id,
            'nombre' => 'Lucas Rossi',
            'telefono' => '1133445566',
            'email' => 'lucas@example.com',
        ]);

        $this->assertDatabaseHas('dispositivos', [
            'marca_y_modelo' => 'Motorola Moto G84',
            'imei_o_serie' => '123456789012345',
        ]);

        $this->assertDatabaseHas('reparaciones', [
            'negocios_id' => $negocio->id,
            'users_id' => $user->id,
            'falla_reportada' => 'Batería se descarga rápido',
            'clave_de_acceso' => '9876',
            'costo_estimado' => 45000,
            'sena' => 15000,
            'notas_internas' => 'El cliente necesita el equipo antes del viernes',
            'estado' => 'recibido',
        ]);

        $reparacion = Reparacion::where('negocios_id', $negocio->id)->first();
        $this->assertNotNull($reparacion);
        $this->assertNotNull($reparacion->codigo_seguimiento);
    }

    public function test_store_reparacion_validation_fails_when_required_fields_are_missing(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->post(route('reparaciones.store'), []);

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

    public function test_store_reparacion_validation_fails_with_invalid_numeric_values(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->post(route('reparaciones.store'), [
            'nombre' => 'Lucas Rossi',
            'telefono' => '1133445566',
            'marca_y_modelo' => 'Motorola Moto G84',
            'falla_reportada' => 'Batería no carga',
            'sena' => 'invalido',
            'costo_estimado' => 'texto',
        ]);

        $response->assertSessionHasErrors(['sena', 'costo_estimado']);
        $this->assertDatabaseCount('reparaciones', 0);
    }

    public function test_guest_cannot_update_reparacion_estado(): void
    {
        $reparacion = Reparacion::factory()->create();

        $response = $this->patchJson(route('reparaciones.update-estado', $reparacion), [
            'estado' => 'en_reparacion',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_reparacion_estado_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'recibido',
        ]);

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update-estado', $reparacion), [
            'estado' => 'en_reparacion',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'estado' => 'En reparación',
            'estado_slug' => 'en_reparacion',
            'dot_color' => 'bg-amber-400',
        ]);

        $reparacion->refresh();
        $this->assertEquals('en_reparacion', $reparacion->estado);
    }

    public function test_user_cannot_update_reparacion_estado_with_invalid_value(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'estado' => 'recibido',
        ]);

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update-estado', $reparacion), [
            'estado' => 'estado_desconocido',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['estado']);

        $reparacion->refresh();
        $this->assertEquals('recibido', $reparacion->estado);
    }

    public function test_user_cannot_update_reparacion_estado_belonging_to_another_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();

        $negocioB = Negocio::factory()->create();
        $userB = User::factory()->conNegocio($negocioB)->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create();
        $dispositivoB = Dispositivo::factory()->paraCliente($clienteB)->create();
        $reparacionB = Reparacion::factory()->paraContexto($negocioB, $dispositivoB, $userB)->create([
            'estado' => 'recibido',
        ]);

        $response = $this->actingAs($userA)->patchJson(route('reparaciones.update-estado', $reparacionB), [
            'estado' => 'en_reparacion',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'No tienes permisos para modificar esta reparación.',
        ]);

        $reparacionB->refresh();
        $this->assertEquals('recibido', $reparacionB->estado);
    }

    public function test_guest_cannot_update_reparacion_notas(): void
    {
        $reparacion = Reparacion::factory()->create();

        $response = $this->patchJson(route('reparaciones.update-notas', $reparacion), [
            'notas_internas' => 'Nota de prueba',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_reparacion_notas_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create([
            'notas_internas' => 'Nota original',
        ]);

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update-notas', $reparacion), [
            'notas_internas' => 'Módulo cambiado y testeado.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Notas técnicas actualizadas correctamente.',
            'notas_internas' => 'Módulo cambiado y testeado.',
        ]);

        $reparacion->refresh();
        $this->assertEquals('Módulo cambiado y testeado.', $reparacion->notas_internas);
    }

    public function test_user_cannot_update_reparacion_notas_belonging_to_another_negocio(): void
    {
        $negocioA = Negocio::factory()->create();
        $userA = User::factory()->conNegocio($negocioA)->create();

        $negocioB = Negocio::factory()->create();
        $userB = User::factory()->conNegocio($negocioB)->create();
        $clienteB = Cliente::factory()->paraNegocio($negocioB)->create();
        $dispositivoB = Dispositivo::factory()->paraCliente($clienteB)->create();
        $reparacionB = Reparacion::factory()->paraContexto($negocioB, $dispositivoB, $userB)->create([
            'notas_internas' => 'Nota de negocio B',
        ]);

        $response = $this->actingAs($userA)->patchJson(route('reparaciones.update-notas', $reparacionB), [
            'notas_internas' => 'Intento de cambio no autorizado',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'No tienes permisos para modificar esta reparación.',
        ]);

        $reparacionB->refresh();
        $this->assertEquals('Nota de negocio B', $reparacionB->notas_internas);
    }

    public function test_update_reparacion_notas_fails_if_text_exceeds_max_length(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();
        $cliente = Cliente::factory()->paraNegocio($negocio)->create();
        $dispositivo = Dispositivo::factory()->paraCliente($cliente)->create();
        $reparacion = Reparacion::factory()->paraContexto($negocio, $dispositivo, $user)->create();

        $response = $this->actingAs($user)->patchJson(route('reparaciones.update-notas', $reparacion), [
            'notas_internas' => str_repeat('a', 2001),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['notas_internas']);
    }
}
