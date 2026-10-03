<?php

use App\Http\Controllers\Api\ReservaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('reservas', ReservaController::class);
