<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExpirationToUserTokens extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user_tokens', [
            'remember_me' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => false,
                'after' => 'device_info'
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'after' => 'remember_me'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user_tokens', ['remember_me', 'expires_at']);
    }
}
