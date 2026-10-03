<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'fecha' => $this->fecha?->format('Y-m-d'),
            'hora' => $this->hora,
            'personas' => $this->personas,
            'ambiente' => $this->ambiente,
            'comentario' => $this->comentario,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
