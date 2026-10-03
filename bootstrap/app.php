<?php

use App\Exceptions\ReservaConflictoException;
use App\Providers\RepositoryServiceProvider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        RepositoryServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'panel.token' => \App\Http\Middleware\VerificarTokenPanel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Renderiza siempre JSON en rutas api/* o cuando el cliente espera JSON
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Excepciones de dominio → 422 Unprocessable Entity
        $exceptions->render(function (ReservaConflictoException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'RESERVA_CONFLICTO',
            ], 422);
        });

        // Modelo no encontrado → 404
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return response()->json([
                'message' => 'Recurso no encontrado.',
                'code' => 'NOT_FOUND',
            ], 404);
        });

        // No autorizado → 403
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
                'code' => 'FORBIDDEN',
            ], 403);
        });

        // Errores de validación → 422 con detalle de campos
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'message' => 'Los datos proporcionados no son válidos.',
                'code' => 'VALIDATION_ERROR',
                'errors' => $e->errors(),
            ], 422);
        });

        // HTTP exceptions genéricas (404 de ruta, 405 método no permitido, etc.)
        $exceptions->render(function (HttpException $e, Request $request) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Error en la solicitud.',
                'code' => 'HTTP_ERROR',
            ], $e->getStatusCode());
        });
    })->create();
