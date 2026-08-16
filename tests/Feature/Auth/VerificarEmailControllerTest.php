<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyEmailCode;
use App\Models\EmailVerificationCode;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VerificarEmailControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_verification_screen(): void
    {
        $response = $this->get(route('verificar-email.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_authenticated_user_can_view_verification_screen(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verificar-email.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.verificar-email');
        $response->assertSee('Verifica tu Email');
    }

    public function test_already_verified_user_without_negocio_is_redirected_to_negocios(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'negocios_id' => null,
        ]);

        $response = $this->actingAs($user)->get(route('verificar-email.index'));

        $response->assertRedirect(route('negocios'));
    }

    public function test_already_verified_user_with_negocio_is_redirected_to_dashboard(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Mi Negocio Test',
            'direccion' => 'Calle Falsa 123',
            'telefono' => '123456789',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'negocios_id' => $negocio->id,
        ]);

        $response = $this->actingAs($user)->get(route('verificar-email.index'));

        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_user_can_verify_email_with_valid_code(): void
    {
        $user = User::factory()->unverified()->create([
            'negocios_id' => null,
        ]);

        $plainCode = '12345';
        $user->emailVerificationCodes()->create([
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post(route('verificar-email.store'), [
            'code' => $plainCode,
        ]);

        $response->assertRedirect(route('negocios'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseMissing('email_verification_codes', [
            'users_id' => $user->id,
        ]);
    }

    public function test_user_can_verify_email_with_digits_array(): void
    {
        $user = User::factory()->unverified()->create([
            'negocios_id' => null,
        ]);

        $plainCode = '54321';
        $user->emailVerificationCodes()->create([
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post(route('verificar-email.store'), [
            'digits' => ['5', '4', '3', '2', '1'],
        ]);

        $response->assertRedirect(route('negocios'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_verified_user_with_negocio_redirects_to_dashboard(): void
    {
        $negocio = Negocio::create([
            'nombre' => 'Taller Express',
            'direccion' => 'Calle 10 456',
            'telefono' => '987654321',
        ]);

        $user = User::factory()->unverified()->create([
            'negocios_id' => $negocio->id,
        ]);

        $plainCode = '78901';
        $user->emailVerificationCodes()->create([
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post(route('verificar-email.store'), [
            'code' => $plainCode,
        ]);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success');
    }

    public function test_verification_fails_with_invalid_code(): void
    {
        $user = User::factory()->unverified()->create();

        $user->emailVerificationCodes()->create([
            'code' => Hash::make('12345'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)
            ->from(route('verificar-email.index'))
            ->post(route('verificar-email.store'), [
                'code' => '99999',
            ]);

        $response->assertRedirect(route('verificar-email.index'));
        $response->assertSessionHasErrors(['code']);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_verification_fails_with_expired_code(): void
    {
        $user = User::factory()->unverified()->create();

        $plainCode = '12345';
        $user->emailVerificationCodes()->create([
            'code' => Hash::make($plainCode),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('verificar-email.index'))
            ->post(route('verificar-email.store'), [
                'code' => $plainCode,
            ]);

        $response->assertRedirect(route('verificar-email.index'));
        $response->assertSessionHasErrors(['code']);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_verification_fails_when_no_active_code_exists(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)
            ->from(route('verificar-email.index'))
            ->post(route('verificar-email.store'), [
                'code' => '12345',
            ]);

        $response->assertRedirect(route('verificar-email.index'));
        $response->assertSessionHasErrors(['code']);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_can_resend_verification_code(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create();

        // Create previous code
        $user->emailVerificationCodes()->create([
            'code' => Hash::make('11111'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->actingAs($user)
            ->from(route('verificar-email.index'))
            ->post(route('verificar-email.resend'));

        $response->assertRedirect(route('verificar-email.index'));
        $response->assertSessionHas('success');

        // Only 1 code should exist now
        $this->assertCount(1, $user->emailVerificationCodes()->get());

        Mail::assertSent(VerifyEmailCode::class, function (VerifyEmailCode $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
