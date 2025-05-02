<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BlogController extends Controller
{
    public function index()
    {
        // The client-side JS will fetch the data from the API
        return view('blog/index');
    }
}