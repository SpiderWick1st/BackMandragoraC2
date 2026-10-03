<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'telefono' => ['sometimes', 'string', 'max:20'],
            'correo' => ['sometimes', 'email', 'max:255'],
            'fecha' => ['sometimes', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora' => ['sometimes', 'date_format:H:i'],
            'personas' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'ambiente' => ['sometimes', 'string', 'max:100'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'correo.email' => 'El correo debe tener un formato válido.',
            'fecha.after_or_equal' => 'La fecha de reserva no puede ser en el pasado.',
            'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
            'hora.date_format' => 'La hora debe tener el formato HH:MM.',
            'personas.min' => 'Debe reservar para al menos 1 persona.',
            'personas.max' => 'El máximo de personas por reserva es 50.',
        ];
    }
}
