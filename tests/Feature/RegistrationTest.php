<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/auth/register');

        $response->assertStatus(200);
        $response->assertSee('EnReparacion');
        $response->assertSee('Registrate');
    }

    public function test_new_users_can_register_and_are_authenticated(): void
    {
        $response = $this->post('/auth/register', [
            'name' => 'Usuario Test',
            'email' => 'test@enreparacion.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@enreparacion.com',
            'name' => 'Usuario Test',
        ]);
    }
}
