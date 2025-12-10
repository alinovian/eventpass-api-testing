<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GuestCategories extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100'
            ],
            'priority' => [
                'type' => 'INT',
                'constraint' => 15
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('guestCategories');

        // Insert Data Default
        $this->db->table('guestCategories')->insertBatch([
            ['name' => 'VIP', 'priority' => 1],
            ['name' => 'Reguler', 'priority' => 2],
            ['name' => 'Media', 'priority' => 3],
        ]);
    }
    public function down()
    {
        $this->forge->dropTable('guestCategories');
    }
}
