<?php

namespace Database\Factories;

use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
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

    /**
     * Asocia una suscripción activa (o inactiva) al negocio.
     */
    public function conSuscripcion(?Plan $plan = null, bool $activa = true): static
    {
        return $this->afterCreating(function (Negocio $negocio) use ($plan, $activa) {
            Suscripcion::factory()->create([
                'negocios_id' => $negocio->id,
                'plan_id' => $plan?->id ?? Plan::factory(),
                'estado' => $activa,
            ]);
        });
    }
}
