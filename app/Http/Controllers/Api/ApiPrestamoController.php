<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Categoria;
use App\Models\Movimiento;
use Illuminate\Support\Facades\Validator;

class ApiPrestamoController extends Controller
{
    /**
     * Obtener los datos iniciales necesarios para el formulario de la App
     */
    public function getFormResources()
    {
        // 1. Obtener los contactos ordenados de la tabla personas
        $personas = Persona::orderBy('nombre', 'asc')->get();

        // 2. Obtener las categorías exclusivas de deudas
        $categorias = Categoria::whereIn('nombre', [
            'Préstamo Recibido (Me prestaron)',
            'Préstamo Otorgado (Presté dinero)',
            'Pago de Préstamo (Pagué lo que debía)',
            'Cobro de Préstamo (Me pagaron lo que debían)'
        ])->get();

        return response()->json([
            'status' => 'success',
            'personas' => $personas,
            'categorias' => $categorias
        ], 200);
    }

    /**
     * Guardar un préstamo o abono real enviado desde el dispositivo móvil
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha'           => 'required|date',
            'descripcion'     => 'required|string|max:255',
            'categoria_id'    => 'required|exists:categorias,id',
            'persona_id'      => 'required|exists:personas,id',
            'tipo_movimiento' => 'required|in:PRESTAMO,ABONO',
            'monto'           => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Creamos el movimiento en PostgreSQL usando los datos masivos sanitizados
        $movimiento = Movimiento::create([
            'fecha'           => $request->fecha,
            'descripcion'     => $request->descripcion,
            'categoria_id'    => $request->categoria_id,
            'persona_id'      => $request->persona_id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'monto'           => $request->monto,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Préstamo/Abono guardado exitosamente en la base de datos.',
            'movimiento' => $movimiento->load(['categoria', 'persona'])
        ], 201);
    }

    public function storePersona(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'nombre'   => 'required|string|max:100',
        'telefono' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Guardamos en PostgreSQL
    $persona = Persona::create([
        'nombre' => $request->nombre,
        'telefono' => $request->telefono
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Contacto registrado con éxito.',
        'persona' => $persona
    ], 210);
}
}
