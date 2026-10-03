<?php

declare(strict_types=1);

namespace App\Repositories\Implementations;

use App\Models\Reserva;
use App\Repositories\Interfaces\ReservaRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;

class EloquentReservaRepository implements ReservaRepositoryInterface
{
    public function paginate(int $perPage = 10): Paginator
    {
        return Reserva::orderByDesc('fecha')
            ->orderBy('hora')
            ->simplePaginate($perPage);
    }

    public function findById(int $id): ?Reserva
    {
        return Reserva::find($id);
    }

    public function paginateConFiltros(array $filtros, int $perPage = 15): Paginator
    {
        $busqueda = trim((string) ($filtros['q'] ?? ''));
        $fecha = (string) ($filtros['fecha'] ?? '');
        $ambiente = (string) ($filtros['ambiente'] ?? '');

        return Reserva::query()
            ->when($busqueda !== '', fn ($query) => $query->where(function ($query) use ($busqueda): void {
                $patron = '%'.mb_strtolower($busqueda).'%';

                foreach (['nombre', 'correo', 'telefono'] as $columna) {
                    $query->orWhereRaw("LOWER({$columna}) LIKE ?", [$patron]);
                }
            }))
            ->when($fecha !== '', fn ($query) => $query->whereDate('fecha', $fecha))
            ->when($ambiente !== '', fn ($query) => $query->where('ambiente', $ambiente))
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function ambientesRegistrados(): array
    {
        return Reserva::query()
            ->select('ambiente')
            ->distinct()
            ->orderBy('ambiente')
            ->pluck('ambiente')
            ->all();
    }

    public function create(array $data): Reserva
    {
        return Reserva::create($data);
    }

    public function update(Reserva $reserva, array $data): Reserva
    {
        $reserva->update($data);

        return $reserva->fresh();
    }

    public function delete(Reserva $reserva): bool
    {
        return (bool) $reserva->delete();
    }

    public function existeEnHorario(string $fecha, string $hora, ?int $excluirId = null): bool
    {
        return Reserva::whereDate('fecha', $fecha)
            ->where('hora', $hora)
            ->when($excluirId, fn ($query) => $query->where('id', '!=', $excluirId))
            ->exists();
    }
}
