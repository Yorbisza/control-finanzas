<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'amount',
        'type',
        'category',
        'transaction_date'
    ];

    // Relación inversa: Una transacción le pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
