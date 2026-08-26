@extends('layouts.app')

@section('title', 'Añadir Contacto')

@section('content')
<div class="max-w-md mx-auto">
    <a href="{{ route('prestamos.create') }}" class="text-sm text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1 mb-4 transition">
        ← Volver al Registro de Préstamo
    </a>

    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-700 bg-gray-800/50">
            <h2 class="text-xl font-bold text-white">Nuevo Contacto / Persona</h2>
            <p class="text-gray-400 text-sm mt-1">Registra a quién le debes o quién te debe dinero.</p>
        </div>

        <form action="{{ route('personas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-bold text-gray-300 mb-2">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ej: Juan Pérez" required
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-amber-500 transition">
            </div>

            <div>
                <label for="telefono" class="block text-sm font-bold text-gray-300 mb-2">Teléfono (Opcional)</label>
                <input type="text" name="telefono" id="telefono" placeholder="Ej: 0412-5555555"
                       class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-amber-500 transition">
            </div>

            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 px-4 rounded-lg transition shadow-lg cursor-pointer">
                Guardar Persona
            </button>
        </form>
    </div>
</div>
@endsection
