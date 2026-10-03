<?php

declare(strict_types=1);

namespace App\Http\Requests\Panel;

use App\Http\Requests\Api\UpdateReservaRequest as ApiUpdateReservaRequest;

/**
 * Igual que en la API, el panel permite mover una reserva a una fecha pasada.
 */
class UpdateReservaRequest extends ApiUpdateReservaRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'fecha' => ['sometimes', 'date', 'date_format:Y-m-d'],
        ]);
    }
}