<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
  return $router->app->version();
});


$router->group(['prefix' => '/v1'], function () use ($router) {

  // TIPO USUARIO
  $router->group(['prefix' => '/tipoUsuario'], function () use ($router) {
    $router->get('/', 'TipoUsuarioController@listAll');
    $router->get('/{id}', 'TipoUsuarioController@get');
    $router->post('/', 'TipoUsuarioController@create');
    $router->put('/{id}', 'TipoUsuarioController@update');
    $router->delete('/{id}', 'TipoUsuarioController@delete');
    $router->get('/restore/{id}', 'TipoUsuarioController@restore');
  });

  // TIPO DOCUMENTO
  $router->group(['prefix' => '/tipoDocumento'], function () use ($router) {
    $router->get('/', 'TipoDocumentoController@index');
    $router->get('/{id}', 'TipoDocumentoController@show');
    $router->post('/', 'TipoDocumentoController@store');
    $router->put('/{id}', 'TipoDocumentoController@update');
    $router->delete('/{id}', 'TipoDocumentoController@destroy');
  });
});
