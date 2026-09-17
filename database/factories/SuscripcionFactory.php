<?php

namespace Database\Factories;

use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Suscripcion>
 */
class SuscripcionFactory extends Factory
{
    protected $model = Suscripcion::class;

    public function definition(): array
    {
        $inicio = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'negocios_id' => Negocio::factory(),
            'plan_id' => Plan::factory(),
            'estado' => true,
            'inicio' => $inicio,
            'fin' => (clone $inicio)->modify('+1 month'),
            'ultimo_pago' => $inicio,
            'proxima_facturacion' => (clone $inicio)->modify('+1 month'),
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => false,
        ]);
    }
}
