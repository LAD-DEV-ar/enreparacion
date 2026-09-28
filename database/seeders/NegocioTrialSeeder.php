<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Database\Seeder;

class NegocioTrialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminFijo = User::firstWhere('email', 'test_user_2887582496948464712@testuser.com');

        if (! $adminFijo) {

            $negocioPrincipal = Negocio::factory()
                ->conSuscripcionTrial()
                ->create([
                    'nombre' => 'Mi Taller de Prueba',
                    'direccion' => 'Av. Siempre Viva 742',
                    'telefono' => '0351-4123456',
                ]);

            $adminFijo = User::factory()
                ->administrador()
                ->conNegocio($negocioPrincipal)
                ->create([
                    'name' => 'Admin Principal',
                    'email' => 'test_user_2887582496948464712@testuser.com',
                    'password' => bcrypt('password'),
                ]);
            $this->command?->info("✔  Negocio principal con plan trial: {$negocioPrincipal->nombre} (ID {$negocioPrincipal->id})");
            $this->command?->info("✔  Admin fijo: {$adminFijo->email} / contraseña: password");
        }
    }
}
