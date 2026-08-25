<?php

namespace Database\Factories;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'               => fake('es_AR')->name(),
            'email'              => fake()->unique()->safeEmail(),
            'telefono'           => fake('es_AR')->phoneNumber(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'rol'                => 'empleado',
            'remember_token'     => Str::random(10),
        ];
    }

    /**
     * Indica que el email no ha sido verificado.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Asignar el usuario al rol de administrador.
     */
    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }

    /**
     * Vincular el usuario a un negocio existente.
     */
    public function conNegocio(Negocio $negocio): static
    {
        return $this->state(fn (array $attributes) => [
            'negocios_id' => $negocio->id,
        ]);
    }
}
