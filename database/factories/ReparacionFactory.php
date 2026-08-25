<?php

namespace Database\Factories;

use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Reparacion>
 */
class ReparacionFactory extends Factory
{
    private static array $fallas = [
        'Pantalla rota', 'No enciende', 'Batería no carga', 'Sin señal',
        'Cámara no funciona', 'Micrófono sin sonido', 'Altavoz dañado',
        'Conector de carga roto', 'Botón de volumen trabado',
        'Se apaga solo', 'Pantalla con manchas', 'Touch no responde',
        'Sin WiFi', 'Sin Bluetooth', 'Se calienta demasiado',
        'No toma tarjeta SIM', 'Software dañado / loop de arranque',
        'Vibrador no funciona', 'Flash de la cámara fundido',
    ];

    private static array $estados = [
        'recibido', 'en_diagnostico', 'esperando_repuesto',
        'en_reparacion', 'listo', 'entregado',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $costo = fake()->boolean(80)
            ? fake()->numberBetween(5000, 150000)
            : null;

        $sena = ($costo && fake()->boolean(60))
            ? fake()->numberBetween(0, (int) ($costo * 0.5))
            : null;

        return [
            'negocios_id'       => Negocio::factory(),
            'dispositivos_id'   => Dispositivo::factory(),
            'users_id'          => User::factory(),
            'falla_reportada'   => fake()->randomElement(self::$fallas),
            'clave_de_acceso'   => fake()->boolean(50) ? fake()->numerify('####') : null,
            'estado'            => fake()->randomElement(self::$estados),
            'costo_estimado'    => $costo,
            'sena'              => $sena,
            'notas_internas'    => fake()->boolean(40) ? fake()->sentence(10) : null,
            'codigo_seguimiento' => strtoupper(Str::random(10)),
        ];
    }

    /**
     * Asociar la reparación a un negocio, dispositivo y usuario existentes.
     */
    public function paraContexto(Negocio $negocio, Dispositivo $dispositivo, User $usuario): static
    {
        return $this->state(fn (array $attributes) => [
            'negocios_id'     => $negocio->id,
            'dispositivos_id' => $dispositivo->id,
            'users_id'        => $usuario->id,
        ]);
    }
}
