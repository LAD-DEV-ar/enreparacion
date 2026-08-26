<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Dispositivo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispositivo>
 */
class DispositivoFactory extends Factory
{
    /**
     * Marcas y modelos de dispositivos comunes.
     */
    private static array $marcasModelos = [
        'Samsung Galaxy A54', 'Samsung Galaxy S23', 'Samsung Galaxy A14',
        'iPhone 13', 'iPhone 14', 'iPhone 12', 'iPhone 11', 'iPhone SE',
        'Motorola Moto G84', 'Motorola Moto G54', 'Motorola Edge 40',
        'Xiaomi Redmi Note 12', 'Xiaomi 13T', 'Redmi 12C',
        'LG K62', 'LG Velvet',
        'Nokia G21', 'Nokia G60',
        'OPPO A98', 'Realme 11 Pro',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clientes_id' => Cliente::factory(),
            'marca_y_modelo' => fake()->randomElement(self::$marcasModelos),
            'imei_o_serie' => fake()->boolean(70)
                ? fake()->numerify('##############') // IMEI de 14 dígitos
                : null,
        ];
    }

    /**
     * Asociar el dispositivo a un cliente existente.
     */
    public function paraCliente(Cliente $cliente): static
    {
        return $this->state(fn (array $attributes) => [
            'clientes_id' => $cliente->id,
        ]);
    }
}
