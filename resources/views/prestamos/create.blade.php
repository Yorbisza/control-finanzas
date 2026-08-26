@extends('layouts.app')

@section('title', 'Registrar Préstamo')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('prestamos.index') }}" class="text-sm text-amber-600 dark:text-amber-400 hover:underline font-semibold flex items-center gap-1 mb-4 transition">
        ← Volver a Préstamos
    </a>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden transition-colors duration-200">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Registrar Préstamo o Abono</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Controla los flujos de dinero con terceros.</p>
        </div>

        <form action="{{ route('movimientos.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="fecha" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                           class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
                </div>
                <div>
                    <label for="monto" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Monto ($)</label>
                    <input type="number" name="monto" id="monto" step="0.01" min="0.01" placeholder="0.00" value="{{ old('monto') }}"
                           class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white font-semibold focus:outline-none focus:border-amber-500 transition">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="persona_id" class="text-sm font-bold text-gray-700 dark:text-gray-300">Contacto / Persona</label>
                    <a href="{{ route('personas.create') }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">+ Crear Persona Nueva</a>
                </div>
                <select name="persona_id" id="persona_id" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
                    <option value="" disabled selected>Selecciona la persona vinculada...</option>
                    @foreach($personas as $per)
                        <option value="{{ $per->id }}">{{ $per->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="categoria_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tipo de Transacción</label>
                <select name="categoria_id" id="categoria_id" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
                    <option value="" disabled selected>Selecciona el concepto...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tipo_movimiento" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Naturaleza del Registro</label>
                <select name="tipo_movimiento" id="tipo_movimiento" required
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
                    <option value="PRESTAMO">Es la Deuda/Préstamo Inicial</option>
                    <option value="ABONO">Es un Abono/Pago a la deuda</option>
                </select>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" placeholder="Ej: Préstamo para repuestos, abono quincenal..." value="{{ old('descripcion') }}"
                       class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:border-amber-500 transition">
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 px-4 rounded-lg transition shadow-md cursor-pointer">
                Guardar Operación
            </button>
        </form>
    </div>
</div>
@endsection
