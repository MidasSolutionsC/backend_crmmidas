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

  // TIPO USUARIOS
  $router->group(['prefix' => '/tipoUsuario'], function () use ($router) {
    $router->get('/', 'TipoUsuarioController@listAll');
    $router->get('/{id}', 'TipoUsuarioController@get');
    $router->post('/', 'TipoUsuarioController@create');
    $router->put('/{id}', 'TipoUsuarioController@update');
    $router->delete('/{id}', 'TipoUsuarioController@delete');
    $router->get('/restore/{id}', 'TipoUsuarioController@restore');
  });

  // TIPO DOCUMENTOS
  $router->group(['prefix' => '/tipoDocumento'], function () use ($router) {
    $router->get('/', 'TipoDocumentoController@listAll');
    $router->get('/{id}', 'TipoDocumentoController@get');
    $router->post('/', 'TipoDocumentoController@create');
    $router->put('/{id}', 'TipoDocumentoController@update');
    $router->delete('/{id}', 'TipoDocumentoController@delete');
    $router->get('/restore/{id}', 'TipoDocumentoController@restore');
  });

  // USUARIOS
  $router->group(['prefix' => '/usuario'], function () use ($router) {
    $router->post('/login', 'UsuarioController@login');
    $router->get('/', 'UsuarioController@listAll');
    $router->get('/{id}', 'UsuarioController@get');
    $router->post('/', 'UsuarioController@create');
    $router->put('/{id}', 'UsuarioController@update');
    $router->delete('/{id}', 'UsuarioController@delete');
    $router->get('/restore/{id}', 'UsuarioController@restore');
  });
});
