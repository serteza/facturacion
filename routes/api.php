<?php
$api = $app->make(Dingo\Api\Routing\Router::class);

$api->version('v1', function ($api) {

    //login
    $api->post('/login', [
        'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin',
        'as' => 'api.login',
    ]);

    //Facturas
    $api->post('/facturacion', [
        'uses' => 'App\Http\Controllers\FacturasController@facturas',
        'as' => 'api.facturacion'
    ]);

    //Complementos
    $api->post('/complemento_pagos', [
        'uses' => 'App\Http\Controllers\ComplementoPagoController@complementoPagos',
        'as' => 'api.complemento_pagos'
    ]);

    $api->get('/pdf/{rfc}/{uuid}', [
        'uses' => 'App\Http\Controllers\ComplementoPagoController@pdfGenerator',
        'as' => 'api.pdf'
    ]);

    //Files
    $api->post('/keys', [
        'uses' => 'App\Http\Controllers\FilesController@storeKeys',
        'as' => 'api.keys'
    ]);
    //catalogos
    $api->get('/catalogos_sat', [
        'uses' => 'App\Http\Controllers\CatalogosController@catalogos',
        'as' => 'api.catalogos_sat'
    ]);

    $api->get('/cadena', [
        'uses' => 'App\Http\Controllers\FacturasController@facturas',
        'as' => 'api.catalogos_sat'
    ]);
});
