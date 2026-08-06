<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWallets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'balance' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('wallets');
    }


    public function down()
    {
        $this->forge->dropTable('wallets');
    }
}