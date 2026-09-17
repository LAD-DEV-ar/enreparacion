<?php

namespace Tests\Feature\Legales;

use Tests\TestCase;

class LegalesTest extends TestCase
{
    public function test_legales_screen_can_be_rendered(): void
    {
        $response = $this->get('/legales');

        $response->assertStatus(200);
        $response->assertViewIs('legales.legales');
    }

    public function test_privacidad_screen_can_be_rendered(): void
    {
        $response = $this->get('/privacidad');

        $response->assertStatus(200);
        $response->assertViewIs('legales.privacidad');
    }
}
