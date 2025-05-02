<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PostModel;
use OpenApi\Annotations as OA;

class PostController extends ResourceController
{
    protected $postModel;
    protected $format = 'json';

    public function __construct()
    {
        $this->postModel = new PostModel();
    }

    /**
     * Get all posts
     * 
     * @OA\Get(
     *     path="/posts",
     *     tags={"Posts"},
     *     summary="Returns a list of all posts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Sample Post"),
     *                     @OA\Property(property="content", type="string", example="This is a sample post content"),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = $this->request->user;
        $posts = $this->postModel->getPostWithUser($user);
        return $this->respond(['status' => 200, 'data' => $posts]);
    }

    /**
     * Get single post
     * 
     * @OA\Get(
     *     path="/posts/{id}",
     *     tags={"Posts"},
     *     summary="Returns a single post by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Sample Post"),
     *                 @OA\Property(property="content", type="string", example="This is a sample post content"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function show($id = null)
    {
        $user = $this->request->user;
        $post = $this->postModel->getPostWithUser($user, $id);

        if ($post) {
            return $this->respond(['status' => 200, 'data' => $post]);
        }

        return $this->failNotFound('No post found with id ' . $id);
    }

    /**
     * Create a new post
     * 
     * @OA\Post(
     *     path="/posts",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="New Post Title"),
     *             @OA\Property(property="content", type="string", example="This is the content of the new post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="title", type="string", example="New Post Title"),
     *                 @OA\Property(property="content", type="string", example="This is the content of the new post"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function create()
    {
        $user = $this->request->user;
        $rules = $this->postModel->getValidationRules();
        
        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'user_id' => (int)$user['id'],
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content')
        ];

        $post_id = $this->postModel->insert($data);

        if ($post_id) {
            $post = $this->postModel->getPostWithUser($user, $post_id);
            return $this->respondCreated(['status' => 201, 'data' => $post]);
        }

        return $this->failServerError('Failed to create post');
    }

    /**
     * Update post
     * 
     * @OA\Put(
     *     path="/posts/{id}",
     *     tags={"Posts"},
     *     summary="Update an existing post",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="Updated Post Title"),
     *             @OA\Property(property="content", type="string", example="This is the updated content of the post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Post Title"),
     *                 @OA\Property(property="content", type="string", example="This is the updated content of the post"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own the post"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update($id = null)
    {
        $user = $this->request->user;
        $post = $this->postModel->where('user_id', $user['id'])->find($id);
        
        if (!$post) {
            return $this->failNotFound('No post found with id ' . $id);
        }

        // Check if user owns the post
        if ($post['user_id'] != $this->request->getVar('user_id')) {
            return $this->failForbidden('You do not have permission to update this post');
        }

        $data = [
            'title' => $this->request->getVar('title'),
            'content' => $this->request->getVar('content')
        ];

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($this->postModel->update($id, $data)) {
            $post = $this->postModel->getPostWithUser($user, $id);
            return $this->respond(['status' => 200, 'data' => $post]);
        }

        return $this->failServerError('Failed to update post');
    }

    /**
     * Delete post
     * 
     * @OA\Delete(
     *     path="/posts/{id}",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own the post"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function delete($id = null)
    {
        $user = $this->request->user;
        $post = $this->postModel->where('user_id', $user['id'])->find($id);
        if (!$post) {
            return $this->failNotFound('No post found with id ' . $id);
        }

        // Check if user owns the post
        if ($post['user_id'] != $this->request->getVar('user_id')) {
            return $this->failForbidden('You do not have permission to delete this post');
        }

        if ($this->postModel->delete($id)) {
            return $this->respondDeleted(['status' => 200, 'message' => 'Post deleted successfully']);
        }

        return $this->failServerError('Failed to delete post');
    }

    /**
     * Get posts by user
     * 
     * @OA\Get(
     *     path="/users/posts",
     *     tags={"Posts"},
     *     summary="Returns all posts by a specific user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Sample Post"),
     *                     @OA\Property(property="content", type="string", example="This is a sample post content"),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function userPosts()
    {
        $user = $this->request->user;
        $userId = $user['id'];
        $posts = $this->postModel->getPostsByUser($userId);
        return $this->respond(['status' => 200, 'data' => $posts]);
    }
}