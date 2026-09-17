<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('olvide.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.olvide');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'olvide@enreparacion.com',
        ]);

        $response = $this->post(route('olvide.store'), [
            'email' => 'olvide@enreparacion.com',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');
    }

    public function test_forgot_password_fails_with_unregistered_email(): void
    {
        $response = $this->post(route('olvide.store'), [
            'email' => 'noexiste@enreparacion.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'token-de-prueba',
            'email' => 'olvide@enreparacion.com',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.recuperar');
        $response->assertViewHas('token', 'token-de-prueba');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@enreparacion.com',
            'password' => Hash::make('password-anterior'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'reset@enreparacion.com',
            'password' => 'nueva-clave-segura123',
            'password_confirmation' => 'nueva-clave-segura123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('nueva-clave-segura123', $user->password));
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@enreparacion.com',
        ]);

        $response = $this->post(route('password.store'), [
            'token' => 'token-invalido',
            'email' => 'reset@enreparacion.com',
            'password' => 'nueva-clave-segura123',
            'password_confirmation' => 'nueva-clave-segura123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
