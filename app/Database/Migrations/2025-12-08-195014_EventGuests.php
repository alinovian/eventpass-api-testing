<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EventGuests extends Migration
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
            'eventID' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true
            ],
            'guestID' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true
            ],
            'categoryID' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true
            ],
            'qrcode' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'belum_hadir'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('eventID', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guestID', 'guests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('categoryID', 'guestCategories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('eventGuests');
    }
    public function down()
    {
        $this->forge->dropTable('eventGuests');
    }
}
