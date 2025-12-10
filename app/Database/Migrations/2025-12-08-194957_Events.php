<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Events extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'userID' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'date' => ['type' => 'DATE'],
            'time' => ['type' => 'TIME'],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('userID', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('events');
    }
    public function down()
    {
        $this->forge->dropTable('events');
    }
}
