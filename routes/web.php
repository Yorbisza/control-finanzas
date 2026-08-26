<?php

use App\Http\Controllers\MovimientoController;
use Illuminate\Support\Facades\Route;

// Ruta principal: Muestra el tablero/dashboard con el balance general y últimos movimientos
Route::get('/', [MovimientoController::class, 'index'])->name('movimientos.index');

// Rutas para los movimientos comunes (Ingresos y Gastos regulares)
Route::prefix('movimientos')->group(function () {
    Route::get('/crear', [MovimientoController::class, 'create'])->name('movimientos.create');
    Route::post('/', [MovimientoController::class, 'store'])->name('movimientos.store');
});

// Rutas dedicadas a los Préstamos y Deudas (para ver los saldos de tus contactos)
Route::prefix('prestamos')->group(function () {
    // Listado de personas a las que les debes o te deben, con sus saldos netos
    Route::get('/', [MovimientoController::class, 'indexPrestamos'])->name('prestamos.index');

    // Formulario especial para registrar un nuevo préstamo o un abono
    Route::get('/registrar', [MovimientoController::class, 'createPrestamo'])->name('prestamos.create');

    // Detalle o estado de cuenta histórico de una persona en específico (Ej: Ver todo lo de Juan)
    Route::get('/persona/{id}', [MovimientoController::class, 'showPersona'])->name('prestamos.persona');

    // Rutas para registrar nuevos contactos/personas
Route::get('/personas/crear', [MovimientoController::class, 'createPersona'])->name('personas.create');
Route::post('/personas', [MovimientoController::class, 'storePersona'])->name('personas.store');
});
