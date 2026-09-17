<?php

namespace Tests\Unit;

use App\Models\Cliente;
use App\Models\User;
use App\Traits\FormateaFechaArgentina;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    use FormateaFechaArgentina;

    public function test_user_initials_generates_two_letters(): void
    {
        $user = new User(['name' => 'Elias Arroyo']);
        $this->assertEquals('EA', $user->initials());

        $userSingle = new User(['name' => 'Admin']);
        $this->assertEquals('A', $userSingle->initials());
    }

    public function test_cliente_iniciales_attribute(): void
    {
        $cliente = new Cliente(['nombre' => 'Juan Pérez']);
        $this->assertEquals('JP', $cliente->iniciales);

        $clienteSingle = new Cliente(['nombre' => 'Sol']);
        $this->assertEquals('SO', $clienteSingle->iniciales);

        $clienteVacio = new Cliente(['nombre' => '']);
        $this->assertEquals('?', $clienteVacio->iniciales);
    }

    public function test_cliente_whatsapp_url_formatting(): void
    {
        $cliente10 = new Cliente(['telefono' => '1144332211']);
        $this->assertEquals('https://wa.me/5491144332211', $cliente10->whatsapp_url);

        $clienteCon0 = new Cliente(['telefono' => '01144332211']);
        $this->assertEquals('https://wa.me/5491144332211', $clienteCon0->whatsapp_url);

        $clienteCon15 = new Cliente(['telefono' => '15123456789']);
        $this->assertEquals('https://wa.me/549123456789', $clienteCon15->whatsapp_url);

        $clienteVacio = new Cliente(['telefono' => '']);
        $this->assertEquals('#', $clienteVacio->whatsapp_url);
    }

    public function test_formatea_fecha_argentina_helpers(): void
    {
        $fecha = Carbon::create(2026, 9, 17, 15, 30, 0, 'UTC');

        // En UTC 15:30 -> En Argentina (UTC-3) es 12:30
        $formateada = $this->formatearFechaHoraArgentina($fecha);
        $this->assertEquals('17/09/2026 12:30', $formateada);

        $soloFecha = $this->formatearSoloFechaArgentina($fecha);
        $this->assertEquals('17/09/2026', $soloFecha);

        $soloHora = $this->formatearHoraArgentina($fecha);
        $this->assertEquals('12:30', $soloHora);

        $defaultResult = $this->formatearFechaHoraArgentina(null, 'd/m/Y', 'N/A');
        $this->assertEquals('N/A', $defaultResult);
    }
}
