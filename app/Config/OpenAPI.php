<?php

namespace App\Config;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="CI API Documentation",
 *     description="API Documentation for CI API"
 * )
 * 
 * @OA\Server(
 *     description="Local Development Server",
 *     url="http://localhost:8080"
 * )
 * 
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer"
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="An example resource")
 * )
 */
class OpenAPI {}
