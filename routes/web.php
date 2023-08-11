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

$router->get('/api/info', function () use ($router) {
  return $router->app->version();
});


$router->group(['prefix' => '/api/v1'], function () use ($router) {

  // TIPO DE USUARIO
  $router->group(['prefix' => '/typeUser'], function () use ($router) {
    $router->get('/', 'TypeUserController@listAll');
    $router->get('/{id}', 'TypeUserController@get');
    $router->post('/', 'TypeUserController@create');
    $router->put('/{id}', 'TypeUserController@update');
    $router->delete('/{id}', 'TypeUserController@delete');
    $router->get('/restore/{id}', 'TypeUserController@restore');
  });

  // TIPO DE DOCUMENTOS
  $router->group(['prefix' => '/typeDocument'], function () use ($router) {
    $router->get('/', 'TypeDocumentController@listAll');
    $router->get('/{id}', 'TypeDocumentController@get');
    $router->post('/', 'TypeDocumentController@create');
    $router->put('/{id}', 'TypeDocumentController@update');
    $router->delete('/{id}', 'TypeDocumentController@delete');
    $router->get('/restore/{id}', 'TypeDocumentController@restore');
  });

  // GRUPOS
  $router->group(['prefix' => '/group'], function () use ($router) {
    $router->get('/', 'GroupController@listAll');
    $router->get('/{id}', 'GroupController@get');
    $router->post('/', 'GroupController@create');
    $router->put('/{id}', 'GroupController@update');
    $router->delete('/{id}', 'GroupController@delete');
    $router->get('/restore/{id}', 'GroupController@restore');
  });

  // INTEGRANTES
  $router->group(['prefix' => '/member'], function () use ($router) {
    $router->get('/', 'MemberController@listAll');
    $router->get('/{id}', 'MemberController@get');
    $router->post('/', 'MemberController@create');
    $router->put('/{id}', 'MemberController@update');
    $router->delete('/{id}', 'MemberController@delete');
    $router->get('/restore/{id}', 'MemberController@restore');
  });

  // CLIENTES
  $router->group(['prefix' => '/client'], function () use ($router) {
    $router->get('/', 'ClientController@listAll');
    $router->get('/{id}', 'ClientController@get');
    $router->post('/', 'ClientController@create');
    $router->put('/{id}', 'ClientController@update');
    $router->delete('/{id}', 'ClientController@delete');
    $router->get('/restore/{id}', 'ClientController@restore');
  });

  // TPO DE SERVICIOS
  $router->group(['prefix' => '/typeService'], function () use ($router) {
    $router->get('/', 'TypeServiceController@listAll');
    $router->get('/{id}', 'TypeServiceController@get');
    $router->post('/', 'TypeServiceController@create');
    $router->put('/{id}', 'TypeServiceController@update');
    $router->delete('/{id}', 'TypeServiceController@delete');
    $router->get('/restore/{id}', 'TypeServiceController@restore');
  });

  // PRODUCTOS
  $router->group(['prefix' => '/product'], function () use ($router) {
    $router->get('/', 'ProductController@listAll');
    $router->get('/{id}', 'ProductController@get');
    $router->post('/', 'ProductController@create');
    $router->put('/{id}', 'ProductController@update');
    $router->delete('/{id}', 'ProductController@delete');
    $router->get('/restore/{id}', 'ProductController@restore');
  });

  // PROMOCIONES
  $router->group(['prefix' => '/promotion'], function () use ($router) {
    $router->get('/', 'PromotionController@listAll');
    $router->get('/{id}', 'PromotionController@get');
    $router->post('/', 'PromotionController@create');
    $router->put('/{id}', 'PromotionController@update');
    $router->delete('/{id}', 'PromotionController@delete');
    $router->get('/restore/{id}', 'PromotionController@restore');
  });

  // VENTAS
  $router->group(['prefix' => '/sale'], function () use ($router) {
    $router->get('/', 'SaleController@listAll');
    $router->get('/{id}', 'SaleController@get');
    $router->post('/', 'SaleController@create');
    $router->put('/{id}', 'SaleController@update');
    $router->delete('/{id}', 'SaleController@delete');
    $router->get('/restore/{id}', 'SaleController@restore');
  });

  // VENTAS DOCUMENTOS
  $router->group(['prefix' => '/saleDocument'], function () use ($router) {
    $router->get('/', 'SaleDocumentController@listAll');
    $router->get('/{id}', 'SaleDocumentController@get');
    $router->post('/', 'SaleDocumentController@create');
    $router->put('/{id}', 'SaleDocumentController@update');
    $router->delete('/{id}', 'SaleDocumentController@delete');
    $router->get('/restore/{id}', 'SaleDocumentController@restore');
  });

  // VENTAS HISTORIAL
  $router->group(['prefix' => '/saleHistory'], function () use ($router) {
    $router->get('/', 'SaleHistoryController@listAll');
    $router->get('/{id}', 'SaleHistoryController@get');
    $router->post('/', 'SaleHistoryController@create');
    $router->put('/{id}', 'SaleHistoryController@update');
    $router->delete('/{id}', 'SaleHistoryController@delete');
    $router->get('/restore/{id}', 'SaleHistoryController@restore');
  });

  // INSTALACIONES
  $router->group(['prefix' => '/installation'], function () use ($router) {
    $router->get('/', 'InstallationController@listAll');
    $router->get('/{id}', 'InstallationController@get');
    $router->post('/', 'InstallationController@create');
    $router->put('/{id}', 'InstallationController@update');
    $router->delete('/{id}', 'InstallationController@delete');
    $router->get('/restore/{id}', 'InstallationController@restore');
  });

  // SERVICIOS
  $router->group(['prefix' => '/service'], function () use ($router) {
    $router->get('/', 'ServiceController@listAll');
    $router->get('/{id}', 'ServiceController@get');
    $router->post('/', 'ServiceController@create');
    $router->put('/{id}', 'ServiceController@update');
    $router->delete('/{id}', 'ServiceController@delete');
    $router->get('/restore/{id}', 'ServiceController@restore');
  });


  // UserS
  $router->group(['prefix' => '/user'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->get('/logout/{id}', 'AuthController@logout');
    $router->post('/', 'UserController@create');
    
    $router->get('/', 'UserController@listAll');
    $router->get('/{id}', 'UserController@get');
    $router->put('/{id}', 'UserController@update');
    $router->delete('/{id}', 'UserController@delete');
    $router->get('/restore/{id}', 'UserController@restore');
    // $router->group(['middleware' => 'auth'], function () use ($router) {
    // });
  });
});
