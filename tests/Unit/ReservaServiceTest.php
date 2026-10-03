<?php

declare(strict_types=1);

use App\Exceptions\ReservaConflictoException;
use App\Models\Reserva;
use App\Repositories\Interfaces\ReservaRepositoryInterface;
use App\Services\ReservaService;

/*
|--------------------------------------------------------------------------
| Unit Tests — ReservaService
|--------------------------------------------------------------------------
| Prueba la lógica de negocio del servicio aislando el repositorio con mocks.
| Usa el TestCase de Laravel para disponer de DB::transaction().
*/

beforeEach(function (): void {
    $this->repositoryMock = Mockery::mock(ReservaRepositoryInterface::class);
    $this->service = new ReservaService($this->repositoryMock);
});

test('crea una reserva cuando no hay conflicto de horario', function (): void {
    $datos = [
        'nombre' => 'Ana García',
        'telefono' => '0991234567',
        'correo' => 'ana@ejemplo.com',
        'fecha' => '2026-12-01',
        'hora' => '19:00',
        'personas' => 4,
        'ambiente' => 'terraza',
        'comentario' => null,
    ];

    $reservaEsperada = Reserva::factory()->make($datos);

    // withArgs valida los dos primeros argumentos; el tercero (null) es opcional
    $this->repositoryMock
        ->shouldReceive('existeEnHorario')
        ->once()
        ->withArgs(fn ($fecha, $hora, $excluirId = null) => $fecha === '2026-12-01' && $hora === '19:00')
        ->andReturn(false);

    $this->repositoryMock
        ->shouldReceive('create')
        ->once()
        ->with($datos)
        ->andReturn($reservaEsperada);

    $resultado = $this->service->crear($datos);

    expect($resultado)->toBeInstanceOf(Reserva::class)
        ->and($resultado->nombre)->toBe('Ana García');
});

test('lanza ReservaConflictoException cuando ya existe una reserva en ese horario', function (): void {
    $datos = [
        'nombre' => 'Carlos López',
        'telefono' => '0997654321',
        'correo' => 'carlos@ejemplo.com',
        'fecha' => '2026-12-01',
        'hora' => '19:00',
        'personas' => 2,
        'ambiente' => 'interior',
        'comentario' => null,
    ];

    $this->repositoryMock
        ->shouldReceive('existeEnHorario')
        ->once()
        ->withArgs(fn ($fecha, $hora, $excluirId = null) => $fecha === '2026-12-01' && $hora === '19:00')
        ->andReturn(true);

    $this->repositoryMock
        ->shouldNotReceive('create');

    expect(fn () => $this->service->crear($datos))
        ->toThrow(ReservaConflictoException::class);
});

test('actualiza una reserva cuando no hay conflicto con otro registro', function (): void {
    $reservaExistente = Reserva::factory()->make([
        'nombre' => 'María Pérez',
        'fecha' => '2026-11-15',
        'hora' => '20:00',
    ]);
    $reservaExistente->id = 5;

    $datos = ['hora' => '21:00'];

    $reservaActualizada = Reserva::factory()->make([
        'nombre' => 'María Pérez',
        'fecha' => '2026-11-15',
        'hora' => '21:00',
    ]);

    $this->repositoryMock
        ->shouldReceive('existeEnHorario')
        ->once()
        ->withArgs(fn ($fecha, $hora, $excluirId) => $fecha === '2026-11-15' && $hora === '21:00' && $excluirId === 5)
        ->andReturn(false);

    $this->repositoryMock
        ->shouldReceive('update')
        ->once()
        ->with($reservaExistente, $datos)
        ->andReturn($reservaActualizada);

    $resultado = $this->service->actualizar($reservaExistente, $datos);

    expect($resultado->hora)->toBe('21:00');
});
