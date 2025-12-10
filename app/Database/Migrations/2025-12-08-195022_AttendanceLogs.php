<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttendanceLogs extends Migration
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
            'eventGuestID' => [
                'type' => 'INT',
                'constraint' => 15,
                'unsigned' => true
            ],
            'scannedTime' => ['type' => 'TIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('eventGuestID', 'eventGuests', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendanceLogs');
    }
    public function down()
    {
        $this->forge->dropTable('attendanceLogs');
    }
}
