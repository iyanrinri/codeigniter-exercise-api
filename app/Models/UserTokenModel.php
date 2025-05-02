<?php

namespace App\Models;

use CodeIgniter\Model;

class UserTokenModel extends Model
{
    protected $table = 'user_tokens';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'token', 'device_info', 'last_used_at', 'created_at'];
    
    public function createToken($userId, $deviceInfo = null)
    {
        $token = bin2hex(random_bytes(32));
        
        $this->insert([
            'user_id' => $userId,
            'token' => $token,
            'device_info' => $deviceInfo,
            'last_used_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $token;
    }
    
    public function validateToken($token)
    {
        $tokenData = $this->where('token', $token)->first();
        
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
