<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Reserva;
use Illuminate\Contracts\Pagination\Paginator;

interface ReservaRepositoryInterface
{
    public function paginate(int $perPage = 10): Paginator;

    /**
     * Lista reservas applying los filtros indicados:
     *
     * @param  array{q?: string, fecha?: string, ambiente?: string}  $filtros
     */
    public function paginateConFiltros(array $filtros, int $perPage = 15): Paginator;

    /** @return list<string> Valores distintos de ambiente registrados. */
    public function ambientesRegistrados(): array;

    public function findById(int $id): ?Reserva;

    public function create(array $data): Reserva;

    public function update(Reserva $reserva, array $data): Reserva;

    public function delete(Reserva $reserva): bool;

    /**
     * Verifica si existe una reserva activa en la fecha y hora indicadas,
     * opcionalmente excluyendo una reserva por ID (para updates).
     */
    public function existeEnHorario(string $fecha, string $hora, ?int $excluirId = null): bool;
}
