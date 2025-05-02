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
