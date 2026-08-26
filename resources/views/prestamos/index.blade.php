@extends('layouts.app')

@section('title', 'Control de Préstamos y Deudas')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Préstamos y Deudas</h1>
            <p class="text-gray-400 text-sm mt-1">Gestión de activos y pasivos con tus contactos.</p>
        </div>
        <a href="{{ route('prestamos.create') }}" class="bg-amber-600 hover:bg-amber-500 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-lg shadow-amber-900/20">
            🤝 Registrar Préstamo / Abono
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($personas as $per)
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-md flex flex-col justify-between space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $per->nombre }}</h2>
                        <p class="text-xs text-gray-400 mt-0.5">📞 {{ $per->telefono ?? 'Sin teléfono' }}</p>
                    </div>
                    <a href="{{ route('prestamos.persona', $per->id) }}" class="text-xs bg-gray-700 hover:bg-gray-600 text-amber-400 font-bold px-3 py-1.5 rounded-md transition">
                        Ver Historial →
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-gray-900/50 p-4 rounded-lg border border-gray-700/40">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400">Le debo (Pasivo)</span>
                        <div class="text-lg font-black {{ $per->lo_que_yo_le_debo > 0 ? 'text-rose-400' : 'text-gray-500' }} mt-1">
                            ${{ number_format($per->lo_que_yo_le_debo, 2) }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400">Me debe (Activo)</span>
                        <div class="text-lg font-black {{ $per->lo_que_me_debe > 0 ? 'text-emerald-400' : 'text-gray-500' }} mt-1">
                            ${{ number_format($per->lo_que_me_debe, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 bg-gray-800 border border-gray-700 rounded-xl p-8 text-center text-gray-500 font-medium">
                No has registrado ningún contacto o préstamo todavía.
            </div>
        @endforelse
    </div>
</div>
@endsection
