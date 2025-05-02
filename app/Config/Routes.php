<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Auth Routes
$routes->post('/register', 'AuthController::register');
$routes->post('/login', 'AuthController::login');

// Protected Routes
$routes->get('/user', 'AuthController::getUser', ['filter' => 'auth']);
$routes->post('/user/profile-image', 'AuthController::uploadProfileImage', ['filter' => 'auth']);

// Protected Posts Routes
$routes->group('posts', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'PostController::index');
    $routes->post('/', 'PostController::create');
    $routes->get('(:num)', 'PostController::show/$1');
    $routes->put('(:num)', 'PostController::update/$1');
    $routes->delete('(:num)', 'PostController::delete/$1');
    $routes->get('user/(:num)', 'PostController::userPosts/$1');
});

// Swagger Documentation Routes
$routes->get('/docs', 'SwaggerController::docs');
$routes->get('/swagger/openapi.json', 'SwaggerController::apiDocs');

// Options for CORS
$routes->options('(:any)', function() {
    $response = service('response');
    $response->setHeader('Access-Control-Allow-Origin', '*');
    $response->setHeader('Access-Control-Allow-Headers', '*');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    return $response->setStatusCode(200);
});
