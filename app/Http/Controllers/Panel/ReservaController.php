<?php

declare(strict_types=1);

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\StoreReservaRequest;
use App\Http\Requests\Panel\UpdateReservaRequest;
use App\Models\Reserva;
use App\Services\ReservaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservaController extends Controller
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly ReservaService $reservaService,
    ) {}

    public function index(Request $request): View
    {
        $filtros = $this->filtrosDesde($request);

        return view('panel.reservas.index', [
            'reservas' => $this->reservaService->listarConFiltros($filtros, self::PER_PAGE),
            'ambientes' => $this->reservaService->ambientesRegistrados(),
            'filtros' => $filtros,
            'resumen' => $this->reservaService->resumen(),
        ]);
    }

    public function create(): View
    {
        return view('panel.reservas.create', [
            'reserva' => new Reserva(['personas' => 2]),
            'ambientes' => $this->reservaService->ambientesRegistrados(),
        ]);
    }

    public function store(StoreReservaRequest $request): RedirectResponse
    {
        $reserva = $this->reservaService->crear($request->validated());

        return redirect()
            ->route('panel.reservas.show', ['token' => $request->route('token'), 'reserva' => $reserva])
            ->with('estado', 'Reserva creada correctamente.');
    }

    public function show(Request $request, Reserva $reserva): View
    {
        return view('panel.reservas.show', [
            'reserva' => $reserva,
            'token' => $request->route('token'),
        ]);
    }

    public function edit(Request $request, Reserva $reserva): View
    {
        return view('panel.reservas.edit', [
            'reserva' => $reserva,
            'ambientes' => $this->reservaService->ambientesRegistrados(),
        ]);
    }

    public function update(UpdateReservaRequest $request, Reserva $reserva): RedirectResponse
    {
        $reservaActualizada = $this->reservaService->actualizar($reserva, $request->validated());

        return redirect()
            ->route('panel.reservas.show', ['token' => $request->route('token'), 'reserva' => $reservaActualizada])
            ->with('estado', 'Reserva actualizada correctamente.');
    }

    public function destroy(Request $request, Reserva $reserva): RedirectResponse
    {
        $this->reservaService->eliminar($reserva);

        return redirect()
            ->route('panel.reservas.index', ['token' => $request->route('token')])
            ->with('estado', 'Reserva eliminada correctamente.');
    }

    /**
     * Normaliza los filtros del listado (búsqueda, fecha y ambiente).
     *
     * @return array{q: string, fecha: string, ambiente: string}
     */
    private function filtrosDesde(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'fecha' => (string) $request->query('fecha', ''),
            'ambiente' => (string) $request->query('ambiente', ''),
        ];
    }
}