<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ReservaConflictoException extends RuntimeException
{
    public function __construct(string $fecha, string $hora)
    {
        parent::__construct(
            "Ya existe una reserva para el {$fecha} a las {$hora}. Por favor elige otro horario."
        );
    }
}
