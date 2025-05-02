<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\UserTokenModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = $request->getHeaderLine('Authorization');
        
        if (empty($token)) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'No token provided'
            ]);
        }

        // Remove 'Bearer ' from token
        $token = str_replace('Bearer ', '', $token);
        
        // Validate token
        $userTokenModel = new UserTokenModel();
        $tokenData = $userTokenModel->validateToken($token);
        
        if (!$tokenData) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Invalid token'
            ]);
        }

        // Get user data
        $userModel = new UserModel();
        $user = $userModel->find($tokenData['user_id']);
        
        if (!$user) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        // Add user to request for use in controllers
        $request->user = $user;
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after
    }
}
