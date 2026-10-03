<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ReservaConflictoException;
use App\Models\Reserva;
use App\Repositories\Interfaces\ReservaRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    public function __construct(
        private readonly ReservaRepositoryInterface $reservaRepository,
    ) {}

    public function listar(): Paginator
    {
        return $this->reservaRepository->paginate();
    }

    /**
     * Lista reservas para el panel, aplicando filtros de búsqueda.
     *
     * @param  array{q?: string, fecha?: string, ambiente?: string}  $filtros
     */
    public function listarConFiltros(array $filtros, int $perPage = 15): Paginator
    {
        return $this->reservaRepository->paginateConFiltros($filtros, $perPage);
    }

    /** @return list<string> */
    public function ambientesRegistrados(): array
    {
        return $this->reservaRepository->ambientesRegistrados();
    }

    /**
     * Conteos para las tarjetas de resumen del panel.
     *
     * @return array{total: int, hoy: int, proximas: int, personasHoy: int}
     */
    public function resumen(): array
    {
        $hoy = now()->format('Y-m-d');

        return [
            'total' => Reserva::count(),
            'hoy' => Reserva::whereDate('fecha', $hoy)->count(),
            'proximas' => Reserva::whereDate('fecha', '>', $hoy)->count(),
            'personasHoy' => (int) Reserva::whereDate('fecha', $hoy)->sum('personas'),
        ];
    }

    public function obtener(int $id): ?Reserva
    {
        return $this->reservaRepository->findById($id);
    }

    /**
     * Crea una nueva reserva verificando conflictos de horario.
     *
     * @param  array<string, mixed>  $datos
     *
     * @throws ReservaConflictoException
     */
    public function crear(array $datos): Reserva
    {
        return DB::transaction(function () use ($datos): Reserva {
            if ($this->reservaRepository->existeEnHorario($datos['fecha'], $datos['hora'])) {
                throw new ReservaConflictoException($datos['fecha'], $datos['hora']);
            }

            return $this->reservaRepository->create($datos);
        });
    }

    /**
     * Actualiza una reserva existente verificando conflictos de horario.
     *
     * @param  array<string, mixed>  $datos
     *
     * @throws ReservaConflictoException
     */
    public function actualizar(Reserva $reserva, array $datos): Reserva
    {
        return DB::transaction(function () use ($reserva, $datos): Reserva {
            $fecha = $datos['fecha'] ?? $reserva->fecha->format('Y-m-d');
            $hora = $datos['hora'] ?? $reserva->hora;

            if ($this->reservaRepository->existeEnHorario($fecha, $hora, $reserva->id)) {
                throw new ReservaConflictoException($fecha, $hora);
            }

            return $this->reservaRepository->update($reserva, $datos);
        });
    }

    public function eliminar(Reserva $reserva): bool
    {
        return $this->reservaRepository->delete($reserva);
    }
}
