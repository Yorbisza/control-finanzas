<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ApiPrestamoController;
use App\Http\Controllers\MovimientoController;

// 🚪 Ruta pública para loguearse desde afuera
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 🛡️ Rutas protegidas (Solo entra quien tenga un Token válido)
Route::middleware('auth:sanctum')->group(function () {

    // Ruta de prueba para verificar que el escudo funciona
    Route::get('/user-check', function (Request $request) {
        return response()->json([
            'status' => 'Conectado con éxito a la API',
            'user' => $request->user()
        ]);
    });
    // 💰 Nuevas rutas de Transacciones
    Route::get('/transactions', [TransactionController::class, 'index']);      // Listar y balances
    Route::post('/transactions', [TransactionController::class, 'store']);    // Crear una nueva
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);

    // Rutas de API para control de préstamos desde la App
Route::get('/prestamos/recursos', [ApiPrestamoController::class, 'getFormResources']);
Route::post('/prestamos/guardar', [ApiPrestamoController::class, 'store']);

Route::post('/personas/guardar', [ApiPrestamoController::class, 'storePersona']);

Route::put('/movimientos/{id}', [MovimientoController::class, 'update']);


});

