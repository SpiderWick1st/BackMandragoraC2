<?php

declare(strict_types=1);

use App\Models\Reserva;
use Illuminate\Testing\Fluent\AssertableJson;

/*
|--------------------------------------------------------------------------
| Feature Tests — Endpoint POST /api/reservas
|--------------------------------------------------------------------------
| Prueba el flujo completo HTTP: validación → servicio → repositorio → BD.
| Usa RefreshDatabase para aislar cada prueba.
*/

test('crea una reserva exitosamente y devuelve 201', function (): void {
    $payload = [
        'nombre' => 'Laura Martínez',
        'telefono' => '0991234567',
        'correo' => 'laura@ejemplo.com',
        'fecha' => now()->addDays(5)->format('Y-m-d'),
        'hora' => '19:00',
        'personas' => 4,
        'ambiente' => 'terraza',
        'comentario' => 'Mesa con vista al jardín.',
    ];

    $response = $this->postJson('/api/reservas', $payload);

    $response
        ->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data')
            ->where('data.nombre', 'Laura Martínez')
            ->where('data.correo', 'laura@ejemplo.com')
            ->where('data.personas', 4)
            ->etc()
        );

    $this->assertDatabaseHas('reservas', [
        'correo' => 'laura@ejemplo.com',
        'personas' => 4,
    ]);
});

test('devuelve 422 con errores de validación cuando faltan campos obligatorios', function (): void {
    $response = $this->postJson('/api/reservas', []);

    $response
        ->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('code', 'VALIDATION_ERROR')
            ->has('errors.nombre')
            ->has('errors.correo')
            ->has('errors.fecha')
            ->has('errors.hora')
            ->etc()
        );
});

test('devuelve 422 cuando ya existe una reserva en el mismo horario', function (): void {
    $fecha = now()->addDays(3)->format('Y-m-d');
    $hora = '20:00';

    // Crea una reserva previa con el mismo horario
    Reserva::factory()->enHorario($fecha, $hora)->create();

    $payload = [
        'nombre' => 'Pedro Sánchez',
        'telefono' => '0987654321',
        'correo' => 'pedro@ejemplo.com',
        'fecha' => $fecha,
        'hora' => $hora,
        'personas' => 2,
        'ambiente' => 'interior',
    ];

    $response = $this->postJson('/api/reservas', $payload);

    $response
        ->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('code', 'RESERVA_CONFLICTO')
            ->has('message')
            ->etc()
        );

    // No se creó un segundo registro
    $this->assertDatabaseCount('reservas', 1);
});

test('devuelve 422 cuando la fecha es en el pasado', function (): void {
    $payload = [
        'nombre' => 'Test Pasado',
        'telefono' => '0991111111',
        'correo' => 'test@ejemplo.com',
        'fecha' => now()->subDay()->format('Y-m-d'),
        'hora' => '18:00',
        'personas' => 1,
        'ambiente' => 'interior',
    ];

    $this->postJson('/api/reservas', $payload)
        ->assertStatus(422)
        ->assertJsonPath('code', 'VALIDATION_ERROR');
});
