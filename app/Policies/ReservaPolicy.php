<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;

class ReservaPolicy
{
    /**
     * Solo los administradores (is_admin) pueden ver el listado de todas las reservas.
     * El frontend Astro envía el formulario público sin autenticación,
     * pero la gestión interna requiere un usuario admin.
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Un admin puede ver cualquier reserva.
     */
    public function view(User $user, Reserva $reserva): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Cualquier usuario autenticado puede crear una reserva.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Solo admins pueden actualizar reservas.
     */
    public function update(User $user, Reserva $reserva): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Solo admins pueden eliminar reservas.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        return (bool) $user->is_admin;
    }
}
