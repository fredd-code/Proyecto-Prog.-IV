<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\NominaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // MÓDULOS DE FASE II - SUMATIVA 3
    // ==========================================
    // 1. Módulo de Trabajadores
    Route::resource('trabajadores', TrabajadorController::class)->only(['index', 'create', 'store', 'show']);

    // 2. Módulo de Expediente Digital
    Route::post('/trabajadores/{trabajador}/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
    Route::delete('/expedientes/{expediente}', [ExpedienteController::class, 'destroy'])->name('expedientes.destroy');

    // 3. Módulo de Control de Asistencias y GPS
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias/registrar', [AsistenciaController::class, 'registrar'])->name('asistencias.registrar');

    // 4. Módulo de Nóminas y Recibos
    Route::get('/nominas', [NominaController::class, 'index'])->name('nominas.index');
    Route::get('/nominas/crear', [NominaController::class, 'create'])->name('nominas.create');
    Route::post('/nominas', [NominaController::class, 'store'])->name('nominas.store');
    Route::get('/nominas/{nomina}', [NominaController::class, 'show'])->name('nominas.show');
    Route::get('/nominas/recibo/{detalle}', [NominaController::class, 'recibo'])->name('nominas.recibo');
});
