<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReservaRequest;
use App\Http\Requests\Api\UpdateReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Services\ReservaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservaController extends Controller
{
    public function __construct(
        private readonly ReservaService $reservaService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ReservaResource::collection(
            $this->reservaService->listar()
        );
    }

    public function store(StoreReservaRequest $request): JsonResponse
    {
        $reserva = $this->reservaService->crear($request->validated());

        return (new ReservaResource($reserva))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Reserva $reserva): ReservaResource
    {
        return new ReservaResource($reserva);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva): ReservaResource
    {
        $reservaActualizada = $this->reservaService->actualizar($reserva, $request->validated());

        return new ReservaResource($reservaActualizada);
    }

    public function destroy(Reserva $reserva): JsonResponse
    {
        $this->reservaService->eliminar($reserva);

        return response()->json(['message' => 'Reserva eliminada correctamente.']);
    }
}
