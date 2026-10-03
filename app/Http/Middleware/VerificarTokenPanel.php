<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege el panel de reservas exigiendo que el segmento {token} de la URL
 * coincida con PANEL_RESERVAS_TOKEN. Si no coincide responde 404 para no
 * revelar que el panel existe.
 */
class VerificarTokenPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        $esperado = (string) config('panel.token');

        $recibido = (string) $request->route('token');

        if ($esperado === '' || $recibido === '' || ! hash_equals($esperado, $recibido)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}