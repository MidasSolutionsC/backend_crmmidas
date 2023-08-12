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

  // PAISES
  $router->group(['prefix' => '/country'], function () use ($router) {
    $router->get('/', 'CountryController@listAll');
    $router->get('/{id}', 'CountryController@get');
    $router->post('/', 'CountryController@create');
    $router->put('/{id}', 'CountryController@update');
    $router->delete('/{id}', 'CountryController@delete');
    $router->get('/restore/{id}', 'CountryController@restore');
  });

  // DEPARTAMENTOS
  $router->group(['prefix' => '/department'], function () use ($router) {
    $router->get('/', 'DepartmentController@listAll');
    $router->get('/filterCountry/{countryId}', 'DepartmentController@getFilterByCountry');
    $router->get('/{id}', 'DepartmentController@get');
    $router->post('/', 'DepartmentController@create');
    $router->put('/{id}', 'DepartmentController@update');
    $router->delete('/{id}', 'DepartmentController@delete');
    $router->get('/restore/{id}', 'DepartmentController@restore');
  });

  // PROVINCIAS
  $router->group(['prefix' => '/province'], function () use ($router) {
    $router->get('/', 'ProvinceController@listAll');
    $router->get('/filterDepartment/{departmentId}', 'ProvinceController@getFilterByDepartment');
    $router->get('/{id}', 'ProvinceController@get');
    $router->post('/', 'ProvinceController@create');
    $router->put('/{id}', 'ProvinceController@update');
    $router->delete('/{id}', 'ProvinceController@delete');
    $router->get('/restore/{id}', 'ProvinceController@restore');
  });

  // DISTRITOS
  $router->group(['prefix' => '/district'], function () use ($router) {
    $router->get('/', 'DistrictController@listAll');
    $router->get('/filterProvince/{provinceId}', 'DistrictController@getFilterByProvince');
    $router->get('/{id}', 'DistrictController@get');
    $router->post('/', 'DistrictController@create');
    $router->put('/{id}', 'DistrictController@update');
    $router->delete('/{id}', 'ProvinceController@delete');
    $router->get('/restore/{id}', 'DistrictController@restore');
  });

  // SEDES
  $router->group(['prefix' => '/campus'], function () use ($router) {
    $router->get('/', 'CampusController@listAll');
    $router->get('/{id}', 'CampusController@get');
    $router->post('/', 'CampusController@create');
    $router->put('/{id}', 'CampusController@update');
    $router->delete('/{id}', 'CampusController@delete');
    $router->get('/restore/{id}', 'CampusController@restore');
  });

  // SEDES USUARIOS
  $router->group(['prefix' => '/campusUser'], function () use ($router) {
    $router->get('/', 'CampusUserController@listAll');
    $router->get('/filterCampus/{campusId}', 'CampusUserController@getFilterByCampus');
    $router->get('/{id}', 'CampusUserController@get');
    $router->post('/', 'CampusUserController@create');
    $router->put('/{id}', 'CampusUserController@update');
    $router->delete('/{id}', 'CampusUserController@delete');
    $router->get('/restore/{id}', 'CampusUserController@restore');
  });

  // SEDES USUARIOS
  $router->group(['prefix' => '/permission'], function () use ($router) {
    $router->get('/', 'PermissionController@listAll');
    $router->get('/{id}', 'PermissionController@get');
    $router->post('/', 'PermissionController@create');
    $router->put('/{id}', 'PermissionController@update');
    $router->delete('/{id}', 'PermissionController@delete');
    $router->get('/restore/{id}', 'PermissionController@restore');
  });

  // SEDES USUARIOS
  $router->group(['prefix' => '/typeUserPermission'], function () use ($router) {
    $router->get('/', 'TypeUserPermissionController@listAll');
    $router->get('/filterTypeUser/{typeUserId}', 'TypeUserPermissionController@getFilterByTypeUser');
    $router->get('/{id}', 'TypeUserPermissionController@get');
    $router->post('/', 'TypeUserPermissionController@create');
    $router->put('/{id}', 'TypeUserPermissionController@update');
    $router->delete('/{id}', 'TypeUserPermissionController@delete');
    $router->get('/restore/{id}', 'TypeUserPermissionController@restore');
  });

  // EMPRESAS
  $router->group(['prefix' => '/company'], function () use ($router) {
    $router->get('/', 'CompanyController@listAll');
    $router->get('/{id}', 'CompanyController@get');
    $router->post('/', 'CompanyController@create');
    $router->put('/{id}', 'CompanyController@update');
    $router->delete('/{id}', 'CompanyController@delete');
    $router->get('/restore/{id}', 'CompanyController@restore');
  });

  // PERSONAS
  $router->group(['prefix' => '/person'], function () use ($router) {
    $router->get('/', 'PersonController@listAll');
    $router->get('/{id}', 'PersonController@get');
    $router->post('/', 'PersonController@create');
    $router->put('/{id}', 'PersonController@update');
    $router->delete('/{id}', 'PersonController@delete');
    $router->get('/restore/{id}', 'PersonController@restore');
  });

  // CONTACTOS
  $router->group(['prefix' => '/contact'], function () use ($router) {
    $router->get('/', 'ContactController@listAll');
    $router->get('/filterCompany/{companyId}', 'ContactController@getFilterByCompany');
    $router->get('/filterPerson/{personId}', 'ContactController@getFilterByPerson');
    $router->get('/{id}', 'ContactController@get');
    $router->post('/', 'ContactController@create');
    $router->put('/{id}', 'ContactController@update');
    $router->delete('/{id}', 'ContactController@delete');
    $router->get('/restore/{id}', 'ContactController@restore');
  });


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
