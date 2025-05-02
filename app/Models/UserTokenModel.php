<?php

namespace App\Models;

use CodeIgniter\Model;

class UserTokenModel extends Model
{
    protected $table = 'user_tokens';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'token', 'device_info', 'remember_me', 'expires_at', 'last_used_at', 'created_at'];
    
    public function createToken($userId, $deviceInfo = null, $rememberMe = false)
    {
        $token = bin2hex(random_bytes(32));
        $now = date('Y-m-d H:i:s');
        
        // Set expiration time based on remember_me flag
        $expiresAt = date('Y-m-d H:i:s', 
            strtotime($now . ($rememberMe ? ' +7 days' : ' +2 hours'))
        );
        
        $this->insert([
            'user_id' => $userId,
            'token' => $token,
            'device_info' => $deviceInfo,
            'remember_me' => $rememberMe,
            'expires_at' => $expiresAt,
            'last_used_at' => $now,
            'created_at' => $now
        ]);
        
        return $token;
    }
    
    public function validateToken($token)
    {
        $tokenData = $this->where('token', $token)
                         ->where('expires_at >', date('Y-m-d H:i:s'))
                         ->first();
        
        if ($tokenData) {
            // Update last used timestamp
            $this->update($tokenData['id'], [
                'last_used_at' => date('Y-m-d H:i:s')
            ]);
            
            return $tokenData;
        }
        
        return false;
    }
    
    public function getUserTokens($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
    
    public function removeToken($token)
    {
        return $this->where('token', $token)->delete();
    }
}
