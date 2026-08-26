<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Relación con el usuario (cada usuario solo ve sus cuentas)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('description');
            $table->decimal('amount', 12, 2); // Soporta montos grandes y decimales exactos

            // Tipo de movimiento: 'income' (Ingreso) o 'expense' (Egreso)
            $table->enum('type', ['income', 'expense']);

            // Categoría (ej: Comida, Transporte, Sueldo, Crypto, etc.)
            $table->string('category')->default('Otros');

            $table->date('transaction_date'); // Fecha en la que se hizo el movimiento
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
