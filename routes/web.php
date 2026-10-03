<?php

use App\Http\Controllers\Panel\ReservaController as PanelReservaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Panel oculto de reservas
|--------------------------------------------------------------------------
| El token viaja en la URL (/panel/{token}/reservas). Si no coincide con
| PANEL_RESERVAS_TOKEN, el middleware VerificarTokenPanel responde 404.
*/

Route::prefix('panel/{token}')
    ->middleware('panel.token')
    ->name('panel.')
    ->group(function (): void {
        Route::resource('reservas', PanelReservaController::class)
            ->parameters(['reservas' => 'reserva'])
            ->except('show');

        Route::get('reservas/{reserva}', [PanelReservaController::class, 'show'])
            ->name('reservas.show');

        Route::redirect('', 'reservas')->name('inicio');
    });