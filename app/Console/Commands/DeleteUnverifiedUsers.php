<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:delete-unverified-users')]
#[Description('Elimina usuarios que no verificaron su email dentro del plazo establecido')]
class DeleteUnverifiedUsers extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fechaLimite = now()->subDays(7);

        $usuariosEliminados = User::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<=', $fechaLimite)
            ->delete();

        $this->info("Usuarios eliminados: {$usuariosEliminados}");

        return self::SUCCESS;
    }
}
