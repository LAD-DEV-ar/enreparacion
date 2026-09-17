<?php

namespace Tests\Feature\Cuenta;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CuentaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cuenta_screen(): void
    {
        $response = $this->get(route('cuenta.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_negocio_cannot_access_cuenta_screen(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('cuenta.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_user_without_subscription_is_redirected_to_planes(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->get(route('cuenta.index'));

        $response->assertRedirect(route('planes.index'));
    }

    public function test_user_with_active_subscription_can_view_cuenta_screen(): void
    {
        $negocio = Negocio::factory()->conSuscripcion()->create([
            'nombre' => 'Electro Taller',
        ]);
        $user = User::factory()->conNegocio($negocio)->create([
            'name' => 'Martín Palermo',
        ]);

        $response = $this->actingAs($user)->get(route('cuenta.index'));

        $response->assertStatus(200);
        $response->assertViewIs('home.cuenta');
        $response->assertViewHas('user');
        $response->assertSee('Martín Palermo');
        $response->assertSee('Electro Taller');
    }

    public function test_user_can_update_perfil_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create([
            'name' => 'Nombre Viejo',
            'telefono' => '1100000000',
        ]);

        $response = $this->actingAs($user)->patch(route('cuenta.update-perfil'), [
            'name' => 'Nombre Nuevo',
            'telefono' => '1199887766',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Nombre Nuevo', $user->name);
        $this->assertEquals('1199887766', $user->telefono);
    }

    public function test_update_perfil_fails_when_name_is_missing(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->patch(route('cuenta.update-perfil'), [
            'name' => '',
            'telefono' => '1199887766',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_can_update_negocio_successfully(): void
    {
        $negocio = Negocio::factory()->create([
            'nombre' => 'Negocio Viejo',
            'direccion' => 'Calle Vieja 123',
            'telefono' => '11111111',
        ]);
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->patch(route('cuenta.update-negocio'), [
            'nombre' => 'Negocio Renovado',
            'direccion' => 'Av. Siempre Viva 742',
            'telefono' => '1122334455',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $negocio->refresh();
        $this->assertEquals('Negocio Renovado', $negocio->nombre);
        $this->assertEquals('Av. Siempre Viva 742', $negocio->direccion);
        $this->assertEquals('1122334455', $negocio->telefono);
    }

    public function test_update_negocio_fails_when_name_is_missing(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create();

        $response = $this->actingAs($user)->patch(route('cuenta.update-negocio'), [
            'nombre' => '',
        ]);

        $response->assertSessionHasErrors(['nombre']);
    }

    public function test_user_can_update_password_successfully(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create([
            'password' => Hash::make('clave-actual-123'),
        ]);

        $response = $this->actingAs($user)->patch(route('cuenta.update-password'), [
            'current_password' => 'clave-actual-123',
            'password' => 'nueva-clave-456',
            'password_confirmation' => 'nueva-clave-456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('nueva-clave-456', $user->password));
    }

    public function test_update_password_fails_if_current_password_is_incorrect(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create([
            'password' => Hash::make('clave-correcta-123'),
        ]);

        $response = $this->actingAs($user)->patch(route('cuenta.update-password'), [
            'current_password' => 'clave-incorrecta',
            'password' => 'nueva-clave-456',
            'password_confirmation' => 'nueva-clave-456',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_update_password_fails_if_confirmation_does_not_match(): void
    {
        $negocio = Negocio::factory()->create();
        $user = User::factory()->conNegocio($negocio)->create([
            'password' => Hash::make('clave-correcta-123'),
        ]);

        $response = $this->actingAs($user)->patch(route('cuenta.update-password'), [
            'current_password' => 'clave-correcta-123',
            'password' => 'nueva-clave-456',
            'password_confirmation' => 'otra-clave-diferente',
        ]);

        $response->assertSessionHasErrors(['password']);
    }
}
