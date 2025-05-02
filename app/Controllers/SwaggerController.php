<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SwaggerController extends Controller
{
    public function docs()
    {
        return view('swagger/docs', [
            'specUrl' => base_url('swagger/openapi.json')
        ]);
    }

    public function apiDocs()
    {
        // Enable CORS
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Headers', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');

        // If it's an OPTIONS request, just return headers
        if ($this->request->getMethod(true) === 'OPTIONS') {
            return $this->response->setStatusCode(200);
        }

        $openapi = file_get_contents(FCPATH . 'swagger/openapi.json');
        return $this->response
            ->setJSON(json_decode($openapi))
            ->setContentType('application/json');
    }
}
