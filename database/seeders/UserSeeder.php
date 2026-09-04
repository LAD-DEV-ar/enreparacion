<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $negocioPrincipal = Negocio::factory()->create([
            'nombre' => 'Mi Taller de Prueba',
            'direccion' => 'Av. Siempre Viva 742',
            'telefono' => '0351-4123456',
        ]);

        $adminFijo = User::factory()->administrador()->conNegocio($negocioPrincipal)->create([
            'name' => 'Admin Principal',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->command->info("✔  Negocio principal: {$negocioPrincipal->nombre} (ID {$negocioPrincipal->id})");
        $this->command->info("✔  Admin fijo: {$adminFijo->email} / contraseña: password");
    }
}
