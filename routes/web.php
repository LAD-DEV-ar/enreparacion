<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificarEmailController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Negocios\NegocioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware(['auth', 'negocios_id']);
Route::post('/dashboard', [DashboardController::class, 'store'])->name('dashboard.store');

Route::get('/auth/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/auth/register', [RegisterController::class, 'index'])->name('register.index');
Route::post('/auth/register', [RegisterController::class, 'store'])->name('register.store');

Route::post('/auth/logout', [LoginController::class, 'logout'])->name('login.logout');

Route::get('/tu-negocio', [NegocioController::class, 'index'])->name('negocios')->middleware(['auth', 'have_negocios_id']);
Route::post('/tu-negocio', [NegocioController::class, 'store'])->name('negocios.store');

Route::get('/verificar-email', [VerificarEmailController::class, 'index'])->name('verificar-email.index')->middleware('auth');
Route::post('/verificar-email', [VerificarEmailController::class, 'store'])->name('verificar-email.store');
Route::post('/verificar-email/reenviar', [VerificarEmailController::class, 'resend'])->name('verificar-email.resend');