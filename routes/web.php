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


$router->group(['prefix' => '/v1', 'middleware' => 'cors'], function () use ($router) {

  // TIPO UserS
  $router->group(['prefix' => '/typeUser'], function () use ($router) {
    $router->get('/', 'TypeUserController@listAll');
    $router->get('/{id}', 'TypeUserController@get');
    $router->post('/', 'TypeUserController@create');
    $router->put('/{id}', 'TypeUserController@update');
    $router->delete('/{id}', 'TypeUserController@delete');
    $router->get('/restore/{id}', 'TypeUserController@restore');
  });

  // TIPO DOCUMENTOS
  $router->group(['prefix' => '/typeDocument'], function () use ($router) {
    $router->get('/', 'TypeDocumentController@listAll');
    $router->get('/{id}', 'TypeDocumentController@get');
    $router->post('/', 'TypeDocumentController@create');
    $router->put('/{id}', 'TypeDocumentController@update');
    $router->delete('/{id}', 'TypeDocumentController@delete');
    $router->get('/restore/{id}', 'TypeDocumentController@restore');
  });

  // UserS
  $router->group(['prefix' => '/user'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->get('/logout/{id}', 'AuthController@logout');
    $router->post('/', 'UserController@create');
    
    $router->group(['middleware' => 'auth'], function () use ($router) {
      $router->get('/', 'UserController@listAll');
      $router->get('/{id}', 'UserController@get');
      $router->put('/{id}', 'UserController@update');
      $router->delete('/{id}', 'UserController@delete');
      $router->get('/restore/{id}', 'UserController@restore');
    });
  });
});
