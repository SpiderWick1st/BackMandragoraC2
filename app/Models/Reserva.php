<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ReservaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    /** @use HasFactory<ReservaFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'fecha',
        'hora',
        'personas',
        'ambiente',
        'comentario',
    ];

    protected $casts = [
        'fecha' => 'date',
        'personas' => 'integer',
    ];
}
