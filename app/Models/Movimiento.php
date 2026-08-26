<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    // Permitimos la carga masiva de todos los campos de la tabla
    protected $fillable = [
        'fecha',
        'descripcion',
        'categoria_id',
        'persona_id',
        'tipo_movimiento',
        'monto'
    ];

    // Relación: El movimiento pertenece a una categoría obligatoriamente
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relación: El movimiento puede (o no) pertenecer a una persona (ej: préstamos)
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
