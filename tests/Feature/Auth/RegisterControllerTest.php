<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyEmailCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register.index'));

        $response->assertStatus(200);
        $response->assertSee('EnReparacion');
        $response->assertSee('Registrate');
    }

    public function test_new_users_can_register_and_are_redirected_to_email_verification(): void
    {
        Mail::fake();

        $response = $this->post(route('register.store'), [
            'name' => 'Usuario Test',
            'email' => 'test@enreparacion.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verificar-email.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'test@enreparacion.com',
            'name' => 'Usuario Test',
        ]);

        $user = User::where('email', 'test@enreparacion.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('email_verification_codes', [
            'users_id' => $user->id,
        ]);

        Mail::assertSent(VerifyEmailCode::class, function (VerifyEmailCode $mail) use ($user) {
            return $mail->hasTo('test@enreparacion.com') && $mail->user->id === $user->id;
        });
    }

    public function test_registration_fails_when_required_fields_are_missing(): void
    {
        $response = $this->post(route('register.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_registration_fails_when_email_is_invalid(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email-format',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_fails_if_email_is_already_taken(): void
    {
        User::factory()->create([
            'email' => 'existing@enreparacion.com',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Otro Usuario',
            'email' => 'existing@enreparacion.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_fails_if_password_is_too_short(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Usuario Test',
            'email' => 'shortpass@enreparacion.com',
            'password' => '12345',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
