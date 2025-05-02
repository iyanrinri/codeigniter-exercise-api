<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

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
        $userModel = new UserModel();
        $user = $userModel->where('api_token', $token)->first();
        
        if (!$user) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Invalid token'
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
