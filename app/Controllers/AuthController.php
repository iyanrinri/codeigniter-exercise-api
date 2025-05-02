<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\UserTokenModel;
use OpenApi\Annotations as OA;

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
     *     path="/user/profile-image",
     *     tags={"User"},
     *     summary="Upload profile image",
     *     description="Upload user profile image",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="profile_image",
     *                     type="string",
     *                     format="binary",
     *                     description="Profile image file (jpg, jpeg, png up to 2MB)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profile image updated successfully"),
     *             @OA\Property(property="profile_image", type="string", example="/uploads/profile/user_1_123456.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid file or validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid file format")
     *         )
     *     )
     * )
     */
    public function uploadProfileImage()
    {
        // Get authenticated user
        $user = $this->request->user;

        $img = $this->request->getFile('profile_image');
        
        if (!$img || !$img->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No valid image file uploaded'
            ]);
        }

        // Validate file
        $validationRule = [
            'profile_image' => [
                'label' => 'Profile Image',
                'rules' => 'uploaded[profile_image]|max_size[profile_image,2048]|mime_in[profile_image,image/jpg,image/jpeg,image/png]|ext_in[profile_image,jpg,jpeg,png]'
            ]
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => $this->validator->getError('profile_image')
            ]);
        }

        // Create profile images directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/profile/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate unique filename
        $newName = 'user_' . $user['id'] . '_' . time() . '.' . $img->getExtension();

        // Delete old profile image if exists
        if (!empty($user['profile_image']) && file_exists(WRITEPATH . 'uploads/' . $user['profile_image'])) {
            unlink(WRITEPATH . 'uploads/' . $user['profile_image']);
        }

        // Move file to uploads directory
        $img->move($uploadPath, $newName);

        // Update user profile_image in database
        $relativePath = 'profile/' . $newName;
        $this->userModel->skipValidation(true)->update($user['id'], [
            'profile_image' => $relativePath
        ]);
        // $this->userModel->update($user['id'], ['profile_image' => $relativePath]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Profile image updated successfully',
            'profile_image' => '/uploads/' . $relativePath
        ]);
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
            // Queue registration email
            try {
                $emailHelper = new \App\Helpers\EmailHelper();
                if (!$emailHelper->sendRegistrationEmail([
                    'name' => $data['name'],
                    'email' => $data['email']
                ])) {
                    log_message('error', 'Failed to queue registration email');
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to queue registration email: ' . $e->getMessage());
            }

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
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="remember_me", type="boolean", example=false, description="Keep logged in for 7 days")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="92e741cd9901f6225e65b962c8fdd3083b8f959f82fe5eecadf5d6775830de0d")
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

        $rememberMe = isset($json->remember_me) ? (bool)$json->remember_me : false;

        $user = $this->userModel->where('email', $json->email)->first();
        
        if ($user && $this->userModel->verifyPassword($json->password, $user['password'])) {
            // Get device info from user agent
            $deviceInfo = $this->request->getUserAgent()->getPlatform() . ' - ' . 
                         $this->request->getUserAgent()->getBrowser() . ' ' . 
                         $this->request->getUserAgent()->getVersion();
            
            // Generate new token for this device
            $token = $this->userTokenModel->createToken($user['id'], $deviceInfo, $rememberMe);

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
     *                 @OA\Property(property="profile_image", type="string", example="/uploads/profile/user_1_123456.jpg"),
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
