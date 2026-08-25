<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Dispositivo;
use App\Models\Negocio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NegocioSeeder extends Seeder
{
    /**
     * Cantidad de negocios de prueba a crear.
     * Cada negocio tendrá sus propios usuarios, clientes, dispositivos y reparaciones.
     */
    private const NEGOCIOS = 2;

    private const USUARIOS_POR_NEGOCIO  = 3;
    private const CLIENTES_POR_NEGOCIO  = 10;
    private const DISPOSITIVOS_POR_CLIENTE = 2;   // máx. dispositivos por cliente
    private const REPARACIONES_POR_DISPOSITIVO = 2; // máx. reparaciones por dispositivo

    public function run(): void
    {
        // ─── Usuario administrador fijo para facilitar el login inicial ────────────
        $negocioPrincipal = Negocio::factory()->create([
            'nombre'    => 'Mi Taller de Prueba',
            'direccion' => 'Av. Siempre Viva 742',
            'telefono'  => '0351-4123456',
        ]);

        $adminFijo = User::factory()->administrador()->conNegocio($negocioPrincipal)->create([
            'name'     => 'Admin Principal',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->command->info("✔  Negocio principal: {$negocioPrincipal->nombre} (ID {$negocioPrincipal->id})");
        $this->command->info("✔  Admin fijo: {$adminFijo->email} / contraseña: password");

        // Sembrar el negocio principal con datos de prueba
        $this->sembrarNegocio($negocioPrincipal, $adminFijo);

        // ─── Negocios adicionales totalmente aleatorios ────────────────────────────
        for ($i = 0; $i < self::NEGOCIOS - 1; $i++) {
            $negocio = Negocio::factory()->create();
            $admin   = User::factory()->administrador()->conNegocio($negocio)->create();
            $this->sembrarNegocio($negocio, $admin);
        }

        $this->command->info('✔  Seeder completado con éxito.');
    }

    /**
     * Crea usuarios, clientes, dispositivos y reparaciones para un negocio dado.
     * Todos los registros quedan vinculados entre sí y al negocio.
     */
    private function sembrarNegocio(Negocio $negocio, User $adminDelNegocio): void
    {
        // Usuarios empleados del negocio
        $empleados = User::factory(self::USUARIOS_POR_NEGOCIO - 1)
            ->conNegocio($negocio)
            ->create();

        // Todos los usuarios del negocio (admin + empleados)
        $todosLosUsuarios = $empleados->push($adminDelNegocio);

        // Clientes del negocio
        $clientes = Cliente::factory(self::CLIENTES_POR_NEGOCIO)
            ->paraNegocio($negocio)
            ->create();

        foreach ($clientes as $cliente) {
            // Cada cliente tiene entre 1 y N dispositivos
            $cantDisp = rand(1, self::DISPOSITIVOS_POR_CLIENTE);
            $dispositivos = Dispositivo::factory($cantDisp)
                ->paraCliente($cliente)
                ->create();

            foreach ($dispositivos as $dispositivo) {
                // Cada dispositivo puede tener entre 1 y N reparaciones
                $cantRep = rand(1, self::REPARACIONES_POR_DISPOSITIVO);

                for ($r = 0; $r < $cantRep; $r++) {
                    // El técnico responsable es uno de los usuarios del negocio
                    $tecnico = $todosLosUsuarios->random();

                    Reparacion::factory()
                        ->paraContexto($negocio, $dispositivo, $tecnico)
                        ->create([
                            // Código de seguimiento único de 10 chars alfanumérico en mayúsculas
                            'codigo_seguimiento' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10)),
                        ]);
                }
            }
        }
    }
}
