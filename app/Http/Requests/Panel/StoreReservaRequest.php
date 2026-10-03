<?php

declare(strict_types=1);

namespace App\Http\Requests\Panel;

use App\Http\Requests\Api\StoreReservaRequest as ApiStoreReservaRequest;

/**
 * El panel permite registrar reservas con fecha pasada (reservas atrasadas o
 * cargadas a mano), por eso se relaja la regla after_or_equal de la API.
 */
class StoreReservaRequest extends ApiStoreReservaRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'fecha' => ['required', 'date', 'date_format:Y-m-d'],
        ]);
    }
}