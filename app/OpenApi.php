<?php

namespace App;

/**
 * @OA\Info(
 *     title="CodeIgniter API Documentation",
 *     version="1.0.0",
 *     description="API Documentation for CodeIgniter Project",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8081",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer"
 *     )
 * )
 */
class OpenApi {}
