<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AjusteSalarialController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\ParametroLegalController;
use App\Http\Controllers\EmpresaUsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas de Bienvenida
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Terminal Kiosco de Recepción (Pública o para tablet presencial en sede)
Route::get('/kiosco', [AsistenciaController::class, 'kiosco'])->name('asistencias.kiosco');
Route::post('/kiosco/registrar', [AsistenciaController::class, 'registrarKiosco'])->name('asistencias.kiosco.registrar');

// Autenticación de Operadores
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Rutas Privadas del Sistema
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // 1. PANEL MACRO: GESTIÓN SAAS DE EMPRESAS Y LEYES (SUPERADMIN)
    // ==========================================
    Route::resource('empresas', EmpresaController::class);
    Route::post('/empresas/{empresa}/toggle-estado', [EmpresaController::class, 'toggleEstado'])->name('empresas.toggle_estado');
    Route::post('/empresas/{empresa}/impersonar', [EmpresaController::class, 'impersonar'])->name('empresas.impersonar');
    Route::post('/superadmin/salir-impersonacion', [EmpresaController::class, 'salirImpersonacion'])->name('empresas.salir_impersonacion');

    Route::post('/empresas/{empresa}/sedes', [SedeController::class, 'store'])->name('sedes.store');
    Route::put('/sedes/{sede}', [SedeController::class, 'update'])->name('sedes.update');
    Route::delete('/sedes/{sede}', [SedeController::class, 'destroy'])->name('sedes.destroy');

    Route::resource('leyes', ParametroLegalController::class)->except(['create', 'show', 'edit']);

    // ==========================================
    // 2. PANEL DE EMPRESA: EQUIPO Y PERMISOS RBAC
    // ==========================================
    Route::resource('equipo', EmpresaUsuarioController::class)->names('usuarios_empresa');

    // ==========================================
    // 3. MÓDULO DE TRABAJADORES (CON MULTI-SEDE Y HORARIOS)
    // ==========================================
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador'])
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // ==========================================
    // 4. MÓDULO DE EXPEDIENTE DIGITAL
    // ==========================================
    Route::post('/trabajadores/{trabajador}/expedientes', [ExpedienteController::class, 'store'])->name('expedientes.store');
    Route::delete('/expedientes/{expediente}', [ExpedienteController::class, 'destroy'])->name('expedientes.destroy');

    // ==========================================
    // 5. MÓDULO DE PRÉSTAMOS Y AJUSTES EXTRAS
    // ==========================================
    Route::resource('ajustes', AjusteSalarialController::class)->only(['index', 'create', 'store', 'destroy']);

    // ==========================================
    // 6. MÓDULO DE ASISTENCIA GEOFENCING Y RETIRO
    // ==========================================
    Route::get('/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias/registrar', [AsistenciaController::class, 'registrar'])->name('asistencias.registrar');
    Route::post('/asistencias/retiro', [AsistenciaController::class, 'retiroImprevisto'])->name('asistencias.retiro');

    // ==========================================
    // 7. MÓDULO DE NÓMINAS Y RECIBOS LOTTT
    // ==========================================
    Route::get('/nominas', [NominaController::class, 'index'])->name('nominas.index');
    Route::get('/nominas/crear', [NominaController::class, 'create'])->name('nominas.create');
    Route::post('/nominas', [NominaController::class, 'store'])->name('nominas.store');
    Route::get('/nominas/{nomina}', [NominaController::class, 'show'])->name('nominas.show');
    Route::get('/nominas/recibo/{detalle}', [NominaController::class, 'recibo'])->name('nominas.recibo');
});
