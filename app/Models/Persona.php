<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'personas'; // Opcional, pero buena práctica

    protected $fillable = ['nombre', 'telefono'];

    // Relación: Una persona puede estar involucrada en muchos movimientos (préstamos o abonos)
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}
