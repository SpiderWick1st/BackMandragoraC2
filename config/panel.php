<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Panel oculto de reservas
    |--------------------------------------------------------------------------
    |
    | El panel se sirve bajo /panel/{token}. Si el token de la URL no coincide
    | con el de abajo, el middleware VerificarTokenPanel responde 404.
    |
    */

    'token' => env('PANEL_RESERVAS_TOKEN'),

];