<?php

use App\Http\Controllers\MercadoPago\MercadoPagoWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/mercado-pago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercado-pago');
