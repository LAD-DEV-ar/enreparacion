<?php

namespace Database\Seeders;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTrialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarioPrincipal = User::firstWhere('email', 'test_user_2887582496948464712@testuser.com');
        if($usuarioPrincipal){
            $this->command->info("Ya existia este usuario registrado/creado en la DB");
            return;
        }
        $negocioPrincipal = Negocio::factory()->conSuscripcionTrial()->create([
            'nombre' => 'Mi Taller de Prueba',
            'direccion' => 'Av. Siempre Viva 742',
            'telefono' => '0351-4123456',
        ]);

        $adminFijo = User::factory()->administrador()->conNegocio($negocioPrincipal)->create([
            'name' => 'Admin Principal',
            'email' => 'test_user_2887582496948464712@testuser.com',
            'password' => bcrypt('password'),
        ]);

        $this->command->info("✔  Negocio principal: {$negocioPrincipal->nombre} (ID {$negocioPrincipal->id}) y con suscripcion trial/prueba");
        $this->command->info("✔  Admin fijo: {$adminFijo->email} / contraseña: password");
    }
}
