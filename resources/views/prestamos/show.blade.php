@extends('layouts.app')

@section('title', 'Historial - ' . $persona->nombre)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <a href="{{ route('prestamos.index') }}" class="text-sm text-gray-400 hover:text-white font-semibold flex items-center gap-1 transition">
        ← Volver a Préstamos
    </a>

    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-xl">
        <h1 class="text-2xl font-black text-white">Estado de Cuenta: {{ $persona->nombre }}</h1>
        <p class="text-sm text-gray-400 mt-1">Línea de tiempo histórica de deudas y amortizaciones.</p>
    </div>

    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-lg">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-900/50 text-gray-400 uppercase text-xs font-bold border-b border-gray-700">
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Descripción</th>
                    <th class="p-4">Tipo</th>
                    <th class="p-4 text-right">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50 text-sm">
                @forelse($historial as $item)
                    <tr class="hover:bg-gray-700/20 transition">
                        <td class="p-4 text-gray-300">{{ date('d/m/Y', strtotime($item->fecha)) }}</td>
                        <td class="p-4 text-white font-semibold">{{ $item->descripcion }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $item->tipo_movimiento === 'PRESTAMO' ? 'bg-amber-950 text-amber-400 border border-amber-800/60' : 'bg-blue-950 text-blue-400 border border-blue-800/60' }} border">
                                {{ $item->tipo_movimiento }}
                            </span>
                        </td>
                        <td class="p-4 text-right font-bold {{ $item->categoria->tipo === 'INGRESO' ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $item->categoria->tipo === 'INGRESO' ? '+' : '-' }} ${{ number_format($item->monto, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">No hay registros de deudas con esta persona.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
