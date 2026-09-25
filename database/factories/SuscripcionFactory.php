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
        $inicio = now()->subDays(5);

        return [
            'negocios_id' => Negocio::factory(),
            'plan_id' => Plan::factory(),
            'tipo' => 'mercadopago',
            'mp_preapproval_id' => 'preapproval_'.fake()->unique()->numerify('########'),
            'mp_status' => 'authorized',
            'estado' => true,
            'inicio' => $inicio,
            'fin' => $inicio->copy()->addMonth(),
            'ultimo_pago' => $inicio,
            'proxima_facturacion' => $inicio->copy()->addMonth(),
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tipo' => 'trial',
            'mp_preapproval_id' => null,
            'mp_status' => null,
            'estado' => true,
            'inicio' => now(),
            'fin' => now()->addDays(30),
            'ultimo_pago' => null,
            'proxima_facturacion' => now()->addDays(30),
        ]);
    }

    public function trialVencida(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tipo' => 'trial',
            'mp_preapproval_id' => null,
            'mp_status' => null,
            'estado' => true,
            'inicio' => now()->subDays(40),
            'fin' => now()->subDays(10),
            'ultimo_pago' => null,
            'proxima_facturacion' => now()->subDays(10),
        ]);
    }

    public function inactiva(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => false,
            'mp_status' => 'cancelled',
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tipo' => 'mercadopago',
            'mp_status' => 'cancelled',
            'estado' => true,
            'proxima_facturacion' => null,
        ]);
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tipo' => 'mercadopago',
            'mp_status' => 'pending',
            'estado' => false,
        ]);
    }
}
