<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(NegocioSeeder::class);
    }
}
