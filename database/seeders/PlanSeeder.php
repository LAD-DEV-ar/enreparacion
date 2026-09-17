<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'nombre' => 'Plan Inicial',
            'descripcion' => 'Luego del primer mes, la suscripción pasa a costar $22.999 por mes',
            'precio' => '0',
            'activo' => true,
        ]);
        $this->command->info('✔    Ya se inserto los datos del plan inicial');
    }
}
