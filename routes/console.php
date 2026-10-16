<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('users:delete-unverified-users')
    ->daily()
    ->before(function () {
        Log::info('Comienza la tarea de eliminar usuarios');
    })
    ->after(function () {
        Log::info('Finalizó la tarea de eliminar usuarios');
    });
