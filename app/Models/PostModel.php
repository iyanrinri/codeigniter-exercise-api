<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'title', 'content'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        // 'user_id' => 'required|integer',
        'title'   => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Title is required',
            'min_length' => 'Title must be at least 3 characters long',
            'max_length' => 'Title cannot exceed 255 characters'
        ],
        'content' => [
            'required' => 'Content is required',
            'min_length' => 'Content must be at least 10 characters long'
        ]
    ];

    protected $skipValidation = false;

    // Get post with user details
    public function getPostWithUser($user = null, $id = null)
    {
        $this->select('posts.*, users.name as author_name');
        $this->join('users', 'users.id = posts.user_id');
        if ($user !== null) {
            $this->where('users.id', $user['id']);
        }
        
        if ($id !== null) {
            return $this->find($id);
        }
        
        return $this->findAll();
    }

    // Get posts by user ID
    public function getPostsByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
