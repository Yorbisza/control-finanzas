<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('movimientos', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->string('descripcion', 255);
        $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
        $table->foreignId('persona_id')->nullable()->constrained('personas')->onDelete('set null');
        $table->enum('tipo_movimiento', ['REGULAR', 'PRESTAMO', 'ABONO'])->default('REGULAR');
        $table->decimal('monto', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
