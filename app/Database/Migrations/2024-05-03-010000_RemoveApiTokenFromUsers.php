<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveApiTokenFromUsers extends Migration
{
    public function up()
    {
        // Remove the api_token column from users table
        $this->forge->dropColumn('users', 'api_token');
    }

    public function down()
    {
        // Add back the api_token column if needed
        $this->forge->addColumn('users', [
            'api_token' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'unique' => true,
                'after' => 'password'
            ]
        ]);
    }
}
