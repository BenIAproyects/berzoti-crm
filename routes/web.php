<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CampanaController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Webhook de Brevo — sin auth ni CSRF
Route::post('/webhooks/brevo', [WebhookController::class, 'brevo'])->name('webhooks.brevo');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/clientes/importar', [ClienteController::class, 'importar'])->name('clientes.importar')->middleware('permission:clientes.importar');
    Route::get('/clientes/template', [ClienteController::class, 'descargarTemplate'])->name('clientes.template')->middleware('permission:clientes.importar');
    Route::get('/clientes/exportar', [ClienteController::class, 'exportar'])->name('clientes.exportar')->middleware('permission:clientes.exportar');
    Route::resource('clientes', ClienteController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('campanas', CampanaController::class)->only(['index', 'create', 'show', 'edit']);
    Route::resource('plantillas', PlantillaController::class)->only(['index', 'create', 'edit']);
    Route::resource('correos', CorreoController::class)->only(['index']);
    Route::resource('tareas', TareaController::class)->only(['index']);
    Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');
    Route::get('/cotizaciones/{cotizacion}/imprimir', [CotizacionController::class, 'imprimir'])->name('cotizaciones.imprimir');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index')->middleware('permission:reportes.ver');
    Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar')->middleware('permission:reportes.ver');

    Route::middleware('role:administrador|supervisor')->group(function () {
        Route::get('/vendedores', [VendedorController::class, 'index'])->name('vendedores.index');
    });

    Route::middleware('permission:usuarios.ver')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->only(['index']);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
