<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// ⚡ 1. ASEGÚRATE DE QUE ESTA LÍNEA EXACTA ESTÉ AQUÍ ARRIBA:
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // ⚡ 2. INCLUYE HasApiTokens DENTRO DE LOS USES DE LA CLASE:
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
