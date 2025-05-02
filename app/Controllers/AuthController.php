<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\UserTokenModel;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="CI API Documentation",
 *     version="1.0.0"
 * )
 */
class AuthController extends ResourceController
{
    protected $userModel;
    protected $userTokenModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userTokenModel = new UserTokenModel();
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Create a new user account",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register()
    {
        $json = $this->request->getJSON();
        
        $data = [
            'name' => $json->name ?? '',
            'email' => $json->email ?? '',
            'password' => $json->password ?? ''
        ];

        if ($this->userModel->insert($data)) {
            return $this->response->setStatusCode(201)->setJSON([
                'status' => 'success',
                'message' => 'Registration successful',
                'user_id' => $this->userModel->getInsertID()
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $this->userModel->errors()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentication"},
     *     summary="Login to the system",
     *     description="Login with email and password to get API token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful. Token remains valid for multiple sessions and devices.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="92e741cd9901f6225e65b962c8fdd3083b8f959f82fe5eecadf5d6775830de0d"),
     *             @OA\Property(
     *                 property="note",
     *                 type="string",
     *                 example="This token remains valid across browser refreshes and multiple devices"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login()
    {
        $json = $this->request->getJSON();
        
        if (!isset($json->email) || !isset($json->password)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Email and password are required'
            ]);
        }

        $user = $this->userModel->where('email', $json->email)->first();
        
        if ($user && $this->userModel->verifyPassword($json->password, $user['password'])) {
            // Get device info from user agent
            $deviceInfo = $this->request->getUserAgent()->getPlatform() . ' - ' . 
                         $this->request->getUserAgent()->getBrowser() . ' ' . 
                         $this->request->getUserAgent()->getVersion();
            
            // Generate new token for this device
            $token = $this->userTokenModel->createToken($user['id'], $deviceInfo);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token
            ]);
        }

        return $this->response->setStatusCode(401)->setJSON([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     summary="Get user information",
     *     description="Get authenticated user's information",
     *     security={{"bearerAuth": {}}},
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer",
     *         bearerFormat="string",
     *         description="Persistent bearer token that remains valid across sessions and devices"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-02 01:23:16"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-02 01:23:27")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No token provided")
     *         )
     *     )
     * )
     */
    public function getUser()
    {
        // User data is already verified and attached to request by AuthFilter
        $user = $this->request->user;
        
        // Remove sensitive data
        unset($user['password']);
        unset($user['api_token']);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $user
        ]);
    }
}
