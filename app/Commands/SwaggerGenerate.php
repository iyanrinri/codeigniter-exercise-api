<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use OpenApi\Generator;

class SwaggerGenerate extends BaseCommand
{
    protected $group = 'Swagger';
    protected $name = 'swagger:generate';
    protected $description = 'Generate Swagger/OpenAPI documentation from annotations';

    public function run(array $params)
    {
        $openapi = Generator::scan([
            APPPATH . 'Config/OpenAPI.php',
            APPPATH . 'Controllers'
        ]);
        
        // Ensure the swagger directory exists
        if (!is_dir(FCPATH . 'swagger')) {
            mkdir(FCPATH . 'swagger', 0777, true);
        }

        // Save the generated OpenAPI spec
        file_put_contents(
            FCPATH . 'swagger/openapi.json',
            $openapi->toJson()
        );

        CLI::write('Swagger documentation generated successfully!', 'green');
    }
}
