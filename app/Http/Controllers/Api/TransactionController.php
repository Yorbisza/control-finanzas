<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

// 1. Listar transacciones y calcular Balances Reales (Regulares + Préstamos)
    public function index()
    {
        $user = Auth::user();

        // --- 1. BALANCES DE TRANSACCIONES REGULARES ---
        $totalIncome = Transaction::where('user_id', $user->id)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', $user->id)->where('type', 'expense')->sum('amount');

        // --- 2. BALANCES DEL MÓDULO DE PRÉSTAMOS (Filtrado seguro por personas del usuario) ---
        $prestamosIngresos = DB::table('movimientos')
            ->join('personas', 'movimientos.persona_id', '=', 'personas.id')
            ->join('categorias', 'movimientos.categoria_id', '=', 'categorias.id')
            ->where('personas.user_id', $user->id) // 👈 Aquí está el truco: filtramos por la tabla personas
            ->where('categorias.tipo', 'INGRESO')
            ->sum('movimientos.monto');

        $prestamosGastos = DB::table('movimientos')
            ->join('personas', 'movimientos.persona_id', '=', 'personas.id')
            ->join('categorias', 'movimientos.categoria_id', '=', 'categorias.id')
            ->where('personas.user_id', $user->id) // 👈 Igual aquí
            ->where('categorias.tipo', 'GASTO')
            ->sum('movimientos.monto');

        // --- 3. TOTALIZACIÓN GLOBAL UNIFICADA ---
        $finalIncome = (float)$totalIncome + (float)$prestamosIngresos;
        $finalExpense = (float)$totalExpense + (float)$prestamosGastos;
        $netBalance = $finalIncome - $finalExpense;

        // --- 4. HISTORIAL UNIFICADO ---
        // Consulta A: Transacciones regulares
        $regularesQuery = DB::table('transactions')
            ->select(
                'id',
                DB::raw("CONCAT('t_', id) as unique_id"),
                'description',
                'amount',
                'type',
                'category',
                'transaction_date'
            )
            ->where('user_id', $user->id);

        // Consulta B: Movimientos de préstamos (Unidos a personas para filtrar por user_id)
        $prestamosQuery = DB::table('movimientos')
            ->join('personas', 'movimientos.persona_id', '=', 'personas.id')
            ->join('categorias', 'movimientos.categoria_id', '=', 'categorias.id')
            ->select(
                'movimientos.id as id',
                DB::raw("CONCAT('m_', movimientos.id) as unique_id"),
                DB::raw("CONCAT(movimientos.descripcion, ' (', personas.nombre, ')') as description"),
                'movimientos.monto as amount',
                DB::raw("CASE WHEN categorias.tipo = 'INGRESO' THEN 'income' ELSE 'expense' END as type"),
                'categorias.nombre as category',
                'movimientos.fecha as transaction_date'
            )
            ->where('personas.user_id', $user->id); // 👈 Aseguramos el historial privado por usuario

        // Fusionamos ambas consultas de manera limpia
        $allTransactions = $regularesQuery->unionAll($prestamosQuery)
            ->orderBy('transaction_date', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'balance' => [
                'total_income' => $finalIncome,
                'total_expense' => $finalExpense,
                'net_balance' => $netBalance
            ],
            'transactions' => $allTransactions
        ], 200);
    }

    // 2. Guardar una nueva transacción regular desde la App o Web
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'      => 'required|string|max:255',
            'amount'           => 'required|numeric|min:0.01',
            'type'             => 'required|in:income,expense',
            'category'         => 'required|string',
            'transaction_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = Transaction::create([
            'user_id'          => Auth::id(),
            'description'      => $request->description,
            'amount'           => $request->amount,
            'type'             => $request->type,
            'category'         => $request->category,
            'transaction_date' => $request->transaction_date,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transacción registrada con éxito',
            'transaction' => $transaction
        ], 201);
    }

   public function update(Request $request, $id)
{
    $user = Auth::user();

    // Validamos que la transacción pertenezca al usuario conectado
    $transaction = Transaction::where('id', $id)->where('user_id', $user->id)->first();

    if (!$transaction) {
        return response()->json(['status' => 'error', 'message' => 'No encontrado'], 404);
    }

    $request->validate([
        'description' => 'required|string',
        'amount' => 'required|numeric',
        'type' => 'required|string',
        'category' => 'required|string',
    ]);

    $transaction->update([
        'description' => $request->description,
        'amount' => $request->amount,
        'type' => $request->type,
        'category' => $request->category,
    ]);

    // 💡 IMPORTANTE: Retornar status => success para que el fetch de React Native lo lea bien
    return response()->json([
        'status' => 'success',
        'message' => 'Transacción actualizada correctamente',
        'transaction' => $transaction
    ], 200);
}
}
