<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'id' => 1,
            'nombre' => 'Plan Inicial',
            'descripcion' => 'Acceso completo al sistema para gestionar reparaciones, clientes y notificaciones. Se cobra por mes.',
            'precio' => '22999',
            'activo' => true,
        ]);
        $this->command->info('✔    Ya se inserto los datos del plan inicial');
    }
}
