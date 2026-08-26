<?php

namespace Database\Factories;

use App\Models\Negocio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Negocio>
 */
class NegocioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombres = [
            'TechFix Centro', 'Reparaciones Express', 'CelularTech',
            'Service Plus', 'MobiRepair', 'PhoneDoc', 'SmartFix',
            'El Taller Digital', 'RepaRápido', 'Nexo Tecnología',
        ];

        return [
            'nombre' => fake()->randomElement($nombres).' '.fake()->company(),
            'direccion' => fake('es_AR')->streetAddress(),
            'telefono' => fake('es_AR')->phoneNumber(),
        ];
    }
}
