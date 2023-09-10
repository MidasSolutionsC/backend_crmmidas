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

// Mostrar archivos
$router->get('/files/{fileName}', 'FileController@showFile');

// API V1
$router->group(['prefix' => '/api/v1'], function () use ($router) {

  /**
   * NO PROTEGIDAS
   */

   

  /**
   * PROTEGIDAS POR AUTHENTICATION
   */

  // PAISES
  $router->group(['prefix' => '/country'], function () use ($router) {
    $router->get('/', 'CountryController@listAll');
    $router->get('/{id}', 'CountryController@get');
    $router->post('/', 'CountryController@create');
    $router->put('/{id}', 'CountryController@update');
    $router->delete('/{id}', 'CountryController@delete');
    $router->get('/restore/{id}', 'CountryController@restore');
  });

  // UBIGEOS - PERU
  $router->group(['prefix' => '/ubigeo'], function () use ($router) {
    $router->get('/', 'UbigeoController@listAll');
    $router->post('/search', 'UbigeoController@search');
    $router->get('/{ubigeo}', 'UbigeoController@get');
    $router->post('/', 'UbigeoController@create');
    $router->put('/{ubigeo}', 'UbigeoController@update');
    $router->delete('/{ubigeo}', 'UbigeoController@delete');
    $router->get('/restore/{id}', 'UbigeoController@restore');
  });

  // SEDES
  $router->group(['prefix' => '/campus'], function () use ($router) {
    $router->get('/', 'CampusController@listAll');
    $router->get('/{id}', 'CampusController@get');
    $router->post('/', 'CampusController@create');
    $router->post('/{id}', 'CampusController@update');
    $router->delete('/{id}', 'CampusController@delete');
    $router->get('/restore/{id}', 'CampusController@restore');
  });

  // TIPO DE ESTADOS
  $router->group(['prefix' => '/typeStatus'], function () use ($router) {
    $router->get('/', 'TypeStatusController@listAll');
    $router->get('/{id}', 'TypeStatusController@get');
    $router->post('/', 'TypeStatusController@create');
    $router->put('/{id}', 'TypeStatusController@update');
    $router->delete('/{id}', 'TypeStatusController@delete');
    $router->get('/restore/{id}', 'TypeStatusController@restore');
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

  // TIPO DE USUARIO
  $router->group(['prefix' => '/typeUser'], function () use ($router) {
    $router->get('/', 'TypeUserController@listAll');
    $router->get('/{id}', 'TypeUserController@get');
    $router->post('/', 'TypeUserController@create');
    $router->put('/{id}', 'TypeUserController@update');
    $router->delete('/{id}', 'TypeUserController@delete');
    $router->get('/restore/{id}', 'TypeUserController@restore');
  });

  // PERMISOS DE USUARIOS
  $router->group(['prefix' => '/permission'], function () use ($router) {
    $router->get('/', 'PermissionController@listAll');
    $router->get('/{id}', 'PermissionController@get');
    $router->post('/', 'PermissionController@create');
    $router->put('/{id}', 'PermissionController@update');
    $router->delete('/{id}', 'PermissionController@delete');
    $router->get('/restore/{id}', 'PermissionController@restore');
  });

  // PERMISOS TIPO USUARIOS
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

  // DIRECCIONES
  $router->group(['prefix' => '/address'], function () use ($router) {
    $router->get('/', 'AddressController@listAll');
    $router->get('/filterCompany/{companyId}', 'AddressController@getFilterByCompany');
    $router->get('/filterPerson/{personId}', 'AddressController@getFilterByPerson');
    $router->get('/{id}', 'AddressController@get');
    $router->post('/', 'AddressController@create');
    $router->put('/{id}', 'AddressController@update');
    $router->delete('/{id}', 'AddressController@delete');
    $router->get('/restore/{id}', 'AddressController@restore');
  });


  // PROCESO DE LOGIN
  $router->group(['prefix' => '/auth'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->get('/logout/{id}', 'AuthController@logout');
    // $router->post('/register', 'UserController@createComplete');
  });

  // USUARIOS
  $router->group(['prefix' => '/user'], function () use ($router) {
    // $router->post('/login', 'AuthController@login');
    // $router->get('/logout/{id}', 'AuthController@logout');
    // $router->post('/', 'UserController@create');
    $router->post('/register', 'UserController@createComplete');
    $router->get('/index', 'UserController@index');
    $router->get('/serverSide', 'UserController@getAllServerSide');
    
    $router->get('/{id}', 'UserController@get');
    $router->put('/{id}', 'UserController@update');
    $router->put('/update/{id}', 'UserController@updateComplete');
    $router->delete('/{id}', 'UserController@delete');
    $router->get('/restore/{id}', 'UserController@restore');
    
    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
      $router->get('/', 'UserController@listAll');
    });
  });

  // HISTORIAL DE SESIONES
  $router->group(['prefix' => '/sessionHistory'], function () use ($router) {
    $router->get('/', 'SessionHistoryController@listAll');
    $router->get('/filterUser/{userId}', 'SessionHistoryController@getFilterByUser');
    $router->get('/{id}', 'SessionHistoryController@get');
    $router->post('/', 'SessionHistoryController@create');
    $router->put('/{id}', 'SessionHistoryController@update');
    $router->delete('/{id}', 'SessionHistoryController@delete');
    $router->get('/restore/{id}', 'SessionHistoryController@restore');
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

  // OPERADORES
  $router->group(['prefix' => '/operator'], function () use ($router) {
    $router->get('/', 'OperatorController@listAll');
    $router->get('/index', 'OperatorController@index');
    $router->get('/{id}', 'OperatorController@get');
    $router->post('/', 'OperatorController@create');
    $router->put('/{id}', 'OperatorController@update');
    $router->delete('/{id}', 'OperatorController@delete');
    $router->get('/restore/{id}', 'OperatorController@restore');
  });

  // TIPIFICACIONES
  $router->group(['prefix' => '/typificationCall'], function () use ($router) {
    $router->get('/', 'TypificationCallController@listAll');
    $router->get('/index', 'TypificationCallController@index');
    $router->get('/{id}', 'TypificationCallController@get');
    $router->post('/', 'TypificationCallController@create');
    $router->put('/{id}', 'TypificationCallController@update');
    $router->delete('/{id}', 'TypificationCallController@delete');
    $router->get('/restore/{id}', 'TypificationCallController@restore');
  });

  // LLAMADAS
  $router->group(['prefix' => '/call'], function () use ($router) {
    $router->get('/', 'CallController@listAll');
    $router->get('/index', 'CallController@index');
    $router->get('/filterUser/{userId}', 'CallController@getFilterByUser');
    $router->get('/{id}', 'CallController@get');
    $router->post('/', 'CallController@create');
    $router->put('/{id}', 'CallController@update');
    $router->delete('/{id}', 'CallController@delete');
    $router->get('/restore/{id}', 'CallController@restore');
  });

  // CALENDARIOS
  $router->group(['prefix' => '/calendar'], function () use ($router) {
    $router->get('/', 'CalendarController@listAll');
    $router->get('/filterCUser/{userId}', 'CalendarController@getFilterByUser');
    $router->get('/{id}', 'CalendarController@get');
    $router->post('/', 'CalendarController@create');
    $router->put('/{id}', 'CalendarController@update');
    $router->delete('/{id}', 'CalendarController@delete');
    $router->get('/restore/{id}', 'CalendarController@restore');
  });

  // GRUPOS
  $router->group(['prefix' => '/group'], function () use ($router) {
    $router->get('/', 'GroupController@listAll');
    $router->get('/index', 'GroupController@index');
    $router->get('/{id}', 'GroupController@get');
    $router->post('/', 'GroupController@create');
    $router->post('/register', 'GroupController@createComplete');
    $router->put('/{id}', 'GroupController@update');
    $router->put('/update/{id}', 'GroupController@updateComplete');
    $router->delete('/{id}', 'GroupController@delete');
    $router->get('/restore/{id}', 'GroupController@restore');
  });

  // INTEGRANTES
  $router->group(['prefix' => '/member'], function () use ($router) {
    $router->get('/', 'MemberController@listAll');
    $router->get('/filterGroup/{groupId}', 'MemberController@getByGroup');
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

  // TIPO DE CUENTAS BANCARIAS
  $router->group(['prefix' => '/typeBankAccount'], function () use ($router) {
    $router->get('/', 'TypeBankAccountController@listAll');
    $router->get('/{id}', 'TypeBankAccountController@get');
    $router->post('/', 'TypeBankAccountController@create');
    $router->put('/{id}', 'TypeBankAccountController@update');
    $router->delete('/{id}', 'TypeBankAccountController@delete');
    $router->get('/restore/{id}', 'TypeBankAccountController@restore');
  });

  // CUENTAS BANCARIAS
  $router->group(['prefix' => '/bankAccount'], function () use ($router) {
    $router->get('/', 'BankAccountController@listAll');
    $router->get('/filterClient/{clientId}', 'BankAccountController@getFilterByClient');
    $router->get('/{id}', 'BankAccountController@get');
    $router->post('/', 'BankAccountController@create');
    $router->put('/{id}', 'BankAccountController@update');
    $router->delete('/{id}', 'BankAccountController@delete');
    $router->get('/restore/{id}', 'BankAccountController@restore');
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
  $router->group(['prefix' => '/categoryBrand'], function () use ($router) {
    $router->get('/', 'CategoryBrandController@listAll');
    $router->get('/index', 'CategoryBrandController@index');
    $router->get('/{id}', 'CategoryBrandController@get');
    $router->post('/', 'CategoryBrandController@create');
    $router->put('/{id}', 'CategoryBrandController@update');
    $router->delete('/{id}', 'CategoryBrandController@delete');
    $router->get('/restore/{id}', 'CategoryBrandController@restore');
  });

  // MARCAS
  $router->group(['prefix' => '/brand'], function () use ($router) {
    $router->get('/', 'BrandController@listAll');
    $router->get('/index', 'BrandController@index');
    $router->get('/filterCategory/{categoryId}', 'BrandController@getFilterByCategory');
    $router->get('/{id}', 'BrandController@get');
    $router->post('/', 'BrandController@create');
    $router->put('/{id}', 'BrandController@update');
    $router->delete('/{id}', 'BrandController@delete');
    $router->get('/restore/{id}', 'BrandController@restore');
  });

  // PRODUCTOS
  $router->group(['prefix' => '/product'], function () use ($router) {
    $router->get('/', 'ProductController@listAll');
    $router->get('/index', 'ProductController@index');
    $router->get('/{id}', 'ProductController@get');
    $router->post('/', 'ProductController@create');
    $router->post('/register', 'ProductController@createComplete');
    $router->put('/update/{id}', 'ProductController@updateComplete');
    $router->put('/{id}', 'ProductController@update');
    $router->delete('/{id}', 'ProductController@delete');
    $router->get('/restore/{id}', 'ProductController@restore');
  });

  // PRODUCTOS PRECIOS
  $router->group(['prefix' => '/productPrice'], function () use ($router) {
    $router->get('/', 'ProductPriceController@listAll');
    $router->get('/{id}', 'ProductPriceController@get');
    $router->get('/filterProduct/{productId}', 'ProductPriceController@getFilterByProduct');
    $router->post('/', 'ProductPriceController@create');
    $router->put('/{id}', 'ProductPriceController@update');
    $router->delete('/{id}', 'ProductPriceController@delete');
    $router->get('/restore/{id}', 'ProductPriceController@restore');
  });

  // PROMOCIONES
  $router->group(['prefix' => '/promotion'], function () use ($router) {
    $router->get('/', 'PromotionController@listAll');
    $router->get('/index', 'PromotionController@index');
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

  // VENTAS DETALLES
  $router->group(['prefix' => '/saleDetail'], function () use ($router) {
    $router->get('/', 'SaleDetailController@listAll');
    $router->get('/{id}', 'SaleDetailController@get');
    $router->get('/filterSale/{saleId}', 'SaleDetailController@getFilterBySale');
    $router->post('/', 'SaleDetailController@create');
    $router->put('/{id}', 'SaleDetailController@update');
    $router->delete('/{id}', 'SaleDetailController@delete');
    $router->get('/restore/{id}', 'SaleDetailController@restore');
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

  // SERVICIOS COMENTARIOS
  $router->group(['prefix' => '/serviceComment'], function () use ($router) {
    $router->get('/', 'ServiceCommentController@listAll');
    $router->get('/filterService/{serviceId}', 'ServiceCommentController@getFilterByService');
    $router->get('/{id}', 'ServiceCommentController@get');
    $router->post('/', 'ServiceCommentController@create');
    $router->put('/{id}', 'ServiceCommentController@update');
    $router->delete('/{id}', 'ServiceCommentController@delete');
    $router->get('/restore/{id}', 'ServiceCommentController@restore');
  });

  // SERVICIOS HISTORIALES
  $router->group(['prefix' => '/serviceHistory'], function () use ($router) {
    $router->get('/', 'ServiceHistoryController@listAll');
    $router->get('/filterService/{serviceId}', 'ServiceHistoryController@getFilterByService');
    $router->get('/{id}', 'ServiceHistoryController@get');
    $router->post('/', 'ServiceHistoryController@create');
    $router->put('/{id}', 'ServiceHistoryController@update');
    $router->delete('/{id}', 'ServiceHistoryController@delete');
    $router->get('/restore/{id}', 'ServiceHistoryController@restore');
  });

  // MANUALES
  $router->group(['prefix' => '/manual', 'middleware' => 'jwt.auth'], function () use ($router) {
    $router->get('/', 'ManualController@listAll');
    $router->get('/index', 'ManualController@index');
    $router->get('/{id}', 'ManualController@get');
    $router->post('/', 'ManualController@create');
    $router->post('/update/{id}', 'ManualController@update');
    $router->delete('/{id}', 'ManualController@delete');
    $router->get('/restore/{id}', 'ManualController@restore');
  });

  // ANUNCIOS
  $router->group(['prefix' => '/advertisement', 'middleware' => 'jwt.auth'], function () use ($router) {
    $router->get('/', 'AdvertisementController@listAll');
    $router->get('/index', 'AdvertisementController@index');
    $router->get('/{id}', 'AdvertisementController@get');
    $router->post('/', 'AdvertisementController@create');
    $router->post('/update/{id}', 'AdvertisementController@update');
    $router->delete('/{id}', 'AdvertisementController@delete');
    $router->get('/restore/{id}', 'AdvertisementController@restore');
  });
 
});
