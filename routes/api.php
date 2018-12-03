<?php
$api = $app->make(Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {

    $api->post('/complemento_pagos', [
        'uses' => 'App\Http\Controllers\ComplementoPagoController@complementoPagos',
        'as' => 'api.complemento_pagos'
    ]);

    $api->get('/pdf/{rfc}/{uuid}', [
        'uses' => 'App\Http\Controllers\ComplementoPagoController@pdfGenerator',
        'as' => 'api.pdf'
    ]);

});
