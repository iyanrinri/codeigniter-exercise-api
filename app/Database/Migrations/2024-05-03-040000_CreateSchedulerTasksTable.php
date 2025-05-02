<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulerTasksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'task' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'context' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ci_tasks');
    }

    public function down()
    {
        $this->forge->dropTable('ci_tasks');
    }
}
