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
            'descripcion' => 'Luego del primer mes gratis, la suscripción pasa a costar $22.999 por mes',
            'precio' => '22999',
            'activo' => true,
        ]);
        $this->command->info('✔    Ya se inserto los datos del plan inicial');
    }
}
