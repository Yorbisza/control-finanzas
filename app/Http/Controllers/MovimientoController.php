<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    /**
     * Vista Principal: Tablero con balance general y últimos movimientos
     */
    public function index()
    {
        // 1. Obtener los últimos 10 movimientos con su categoría y persona asociada
        $movimientos = Movimiento::with(['categoria', 'persona'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // 2. Calcular Totales Históricos para el bloque de tarjetas superiores
        // Sumamos los montos según el tipo de la categoría vinculada
        $totalIngresos = Movimiento::whereHas('categoria', function ($query) {
            $query->where('tipo', 'INGRESO');
        })->sum('monto');

        $totalGastos = Movimiento::whereHas('categoria', function ($query) {
            $query->where('tipo', 'GASTO');
        })->sum('monto');

        $saldoNeto = $totalIngresos - $totalGastos;

        return view('movimientos.index', compact('movimientos', 'totalIngresos', 'totalGastos', 'saldoNeto'));
    }

    /**
     * Formulario para registrar un movimiento regular (gasto o ingreso común)
     */
    public function create()
    {
        // Traemos solo categorías normales (excluimos las de préstamos/abonos para no confundir)
        $categorias = Categoria::whereNotIn('nombre', [
            'Préstamo Recibido (Me prestaron)',
            'Préstamo Otorgado (Presté dinero)',
            'Pago de Préstamo (Pagué lo que debía)',
            'Cobro de Préstamo (Me pagaron lo que debían)'
        ])->get();

        return view('movimientos.create', compact('categorias'));
    }

    /**
     * Procesar y guardar cualquier tipo de movimiento (regular o préstamo)
     */
    public function store(Request $request)
    {
        // Validación estricta de datos
        $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'monto' => 'required|numeric|min:0.01',
            'persona_id' => 'nullable|exists:personas,id',
            'tipo_movimiento' => 'required|in:INGRESO,EGRESO,PRESTAMO',
        ]);

        // Guardado masivo gracias al $fillable que configuramos
        Movimiento::create($request->all());

        // Redirección inteligente dependiendo de dónde venía el flujo
        if ($request->tipo_movimiento !== 'REGULAR') {
            return redirect()->route('prestamos.index')->with('success', 'Registro de préstamo/abono procesado con éxito.');
        }

        return redirect()->route('movimientos.index')->with('success', 'Movimiento financiero registrado.');
    }

    /**
     * Vista de Préstamos: Listado de personas con sus saldos netos calculados
     */
    public function indexPrestamos()
    {
        // Aquí ocurre la magia contable. Consultamos las personas y calculamos lo que nos deben o debemos:
        // - Si 'Me prestaron' (INGRESO + PRESTAMO) suma a mi deuda con ellos.
        // - Si 'Pagué' (GASTO + ABONO) resta a mi deuda con ellos.
        // - Si 'Presté dinero' (GASTO + PRESTAMO) suma a lo que ellos me deben a mí.
        // - Si 'Me pagaron' (INGRESO + ABONO) resta a lo que ellos me deben a mí.

        $personas = Persona::all()->map(function ($persona) {

            // 1. Dinero que ESTA PERSONA me prestó a mí
            $mePrestaron = Movimiento::where('persona_id', $persona->id)
                ->where('tipo_movimiento', 'PRESTAMO')
                ->whereHas('categoria', function($q){ $q->where('tipo', 'INGRESO'); })
                ->sum('monto');

            // 2. Dinero que YO le devolví a esta persona
            $yoLePague = Movimiento::where('persona_id', $persona->id)
                ->where('tipo_movimiento', 'ABONO')
                ->whereHas('categoria', function($q){ $q->where('tipo', 'GASTO'); })
                ->sum('monto');

            // 3. Dinero que YO le presté a esta persona
            $yoLePreste = Movimiento::where('persona_id', $persona->id)
                ->where('tipo_movimiento', 'PRESTAMO')
                ->whereHas('categoria', function($q){ $q->where('tipo', 'GASTO'); })
                ->sum('monto');

            // 4. Dinero que ESTA PERSONA me devolvió a mí
            $ellaMePago = Movimiento::where('persona_id', $persona->id)
                ->where('tipo_movimiento', 'ABONO')
                ->whereHas('categoria', function($q){ $q->where('tipo', 'INGRESO'); })
                ->sum('monto');

            // Calculamos saldos finales individuales
            $persona->lo_que_yo_le_debo = $mePrestaron - $yoLePague;
            $persona->lo_que_me_debe = $yoLePreste - $ellaMePago;

            return $persona;
        });

        return view('prestamos.index', compact('personas'));
    }

    /**
     * Formulario especial para registrar un Préstamo o un Abono
     */
    public function createPrestamo()
    {
        // Traemos exclusivamente las categorías que manejan deudas
        $categorias = Categoria::whereIn('nombre', [
            'Préstamo Recibido (Me prestaron)',
            'Préstamo Otorgado (Presté dinero)',
            'Pago de Préstamo (Pagué lo que debía)',
            'Cobro de Préstamo (Me pagaron lo que debían)'
        ])->get();

        $personas = Persona::orderBy('nombre', 'asc')->get();

        return view('prestamos.create', compact('categorias', 'personas'));
    }

    /**
     * Ver el estado de cuenta / línea de tiempo histórico de una sola persona
     */
    public function showPersona($id)
    {
        $persona = Persona::findOrFail($id);

        // Obtenemos todo el historial de deudas y abonos con esta persona en específico
        $historial = Movimiento::with('categoria')
            ->where('persona_id', $id)
            ->whereIn('tipo_movimiento', ['PRESTAMO', 'ABONO'])
            ->orderBy('fecha', 'asc')
            ->get();

        return view('prestamos.show', compact('persona', 'historial'));
    }

    /**
 * Formulario para registrar una nueva persona/contacto
 */
public function createPersona()
{
    return view('prestamos.create_persona');
}

/**
 * Guardar la nueva persona en PostgreSQL
 */
public function storePersona(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:100',
        'telefono' => 'nullable|string|max:20',
    ]);

    Persona::create($request->all());

    // Te redirige directo al formulario de préstamos para que sigas con tu registro
    return redirect()->route('prestamos.create')->with('success', 'Contacto registrado con éxito. Ya puedes seleccionarlo.');
}

public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Buscamos el movimiento asegurando que la persona asociada pertenezca al usuario logueado
        $movimiento = Movimiento::where('id', $id)
            ->whereHas('persona', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->first();

        if (!$movimiento) {
            return response()->json(['message' => 'Movimiento no encontrado o no autorizado'], 404);
        }

        $request->validate([
            'monto' => 'required|numeric',
            'descripcion' => 'required|string|max:255',
            // Agrega las validaciones de persona_id o categoria_id si permites cambiarlas
        ]);

        // Actualizamos los campos
        $movimiento->update([
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            // 'persona_id' => $request->persona_id,
            // 'categoria_id' => $request->categoria_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Movimiento de préstamo actualizado correctamente.',
            'movimiento' => $movimiento
        ], 200);
    }
}
