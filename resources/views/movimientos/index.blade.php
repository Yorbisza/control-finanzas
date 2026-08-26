@extends('layouts.app')

@section('title', 'Tablero Principal - Finanzas')

@section('content')
<div class="space-y-8">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Resumen Financiero</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Monitoreo de flujos de caja actuales.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('movimientos.create') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-lg shadow-emerald-600/10">
                + Nuevo Movimiento
            </a>
            <a href="{{ route('prestamos.create') }}" class="bg-amber-600 hover:bg-amber-500 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-lg shadow-amber-600/10">
                🤝 Registrar Préstamo / Abono
            </a>
        </div>
    </div>

    <!-- Bloque de Tarjetas Adaptativas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta Ingresos -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-xl p-5 shadow-xs transition-colors duration-200">
            <span class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 uppercase">Total Ingresos</span>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">${{ number_format($totalIngresos, 2) }}</div>
        </div>
        <!-- Tarjeta Gastos -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-xl p-5 shadow-xs transition-colors duration-200">
            <span class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 uppercase">Total Gastos</span>
            <div class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-2">${{ number_format($totalGastos, 2) }}</div>
        </div>
        <!-- Tarjeta Saldo Neto -->
        <div class="bg-white dark:bg-gray-800 border border-emerald-500/20 dark:border-emerald-500/30 rounded-xl p-5 shadow-xs bg-gradient-to-br from-white to-emerald-50/50 dark:from-gray-800 dark:to-emerald-950/20 transition-colors duration-200">
            <span class="text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 uppercase">Saldo Neto Acumulado</span>
            <div class="text-3xl font-black {{ $saldoNeto >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-2">
                ${{ number_format($saldoNeto, 2) }}
            </div>
        </div>
    </div>

    <!-- Tabla Adaptativa -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-lg transition-colors duration-200">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Últimos Movimientos Registrados</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Mostrando los últimos 10</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 uppercase text-xs font-bold tracking-wider border-b border-gray-200 dark:border-gray-700">
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Descripción</th>
                        <th class="p-4">Categoría</th>
                        <th class="p-4">Contexto / Persona</th>
                        <th class="p-4 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700/50 text-sm">
                    @forelse($movimientos as $mov)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="p-4 text-gray-600 dark:text-gray-300 font-medium whitespace-nowrap">{{ date('d/m/Y', strtotime($mov->fecha)) }}</td>
                            <td class="p-4 text-gray-900 dark:text-white font-semibold">{{ $mov->descripcion }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $mov->categoria->tipo === 'INGRESO' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                                    {{ $mov->categoria->nombre }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600 dark:text-gray-400">
                                @if($mov->persona)
                                    <span class="text-amber-600 dark:text-amber-400 font-medium">👤 {{ $mov->persona->nombre }}</span>
                                    <span class="text-xs block text-gray-400 dark:text-gray-500">({{ $mov->tipo_movimiento }})</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right font-bold text-base {{ $mov->categoria->tipo === 'INGRESO' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $mov->categoria->tipo === 'INGRESO' ? '+' : '-' }} ${{ number_format($mov->monto, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400 dark:text-gray-500 font-medium">
                                No hay transacciones registradas todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
