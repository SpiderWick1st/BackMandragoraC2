<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Reserva;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserva>
 */
class ReservaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'telefono' => fake()->numerify('09########'),
            'correo' => fake()->safeEmail(),
            'fecha' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'hora' => fake()->time('H:i'),
            'personas' => fake()->numberBetween(1, 10),
            'ambiente' => fake()->randomElement(['interior', 'exterior', 'terraza', 'privado']),
            'comentario' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Estado con fecha y hora fijas para pruebas de conflicto.
     */
    public function enHorario(string $fecha, string $hora): static
    {
        return $this->state([
            'fecha' => $fecha,
            'hora' => $hora,
        ]);
    }
}
