<?php

namespace Tests\Feature\Negocios;

use App\Models\Negocio;
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

        $response = $this->actingAs($user)->post(route('negocios.store'), [
            'nombre' => 'Reparaciones Express',
            'direccion' => 'Av. Rivadavia 5000',
            'telefono' => '1198765432',
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success');

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

    public function test_negocio_registration_fails_when_fields_are_missing(): void
    {
        $user = User::factory()->create([
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->post(route('negocios.store'), []);

        $response->assertSessionHasErrors(['nombre', 'direccion', 'telefono']);
        $this->assertDatabaseCount('negocios', 0);
        $user->refresh();
        $this->assertNull($user->negocios_id);
    }
}
