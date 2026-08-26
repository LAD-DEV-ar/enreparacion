<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Negocio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'negocios_id' => Negocio::factory(),
            'nombre' => fake('es_AR')->name(),
            'telefono' => fake('es_AR')->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    /**
     * Asociar el cliente a un negocio existente.
     */
    public function paraNegocio(Negocio $negocio): static
    {
        return $this->state(fn (array $attributes) => [
            'negocios_id' => $negocio->id,
        ]);
    }
}
