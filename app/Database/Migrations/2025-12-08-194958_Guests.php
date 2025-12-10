<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Guests extends Migration
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
            'fullName' => [
                'type' => 'VARCHAR',
                'constraint' => '255'
            ],
            'affiliation' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ],
            'faceEmbedding' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'photoUrl' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('userID', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('guests');
    }
    public function down()
    {
        $this->forge->dropTable('guests');
    }
}
