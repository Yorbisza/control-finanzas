<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = ['nombre', 'tipo'];

    // Relación: Una categoría puede repetirse en muchos movimientos
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}
