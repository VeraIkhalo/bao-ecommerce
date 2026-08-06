<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWalletTransactionsTable extends Migration
{
   public function up()
{
    $this->forge->addField([
        'id' => [
            'type'           => 'INT',
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'wallet_id' => [
            'type'     => 'INT',
            'unsigned' => true,
        ],
        'type' => [
            'type'       => 'ENUM',
            'constraint' => ['credit', 'debit'],
        ],
        'amount' => [
            'type'       => 'DECIMAL',
            'constraint' => '10,2',
        ],
        'description' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
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
    $this->forge->addKey('wallet_id');

    $this->forge->addForeignKey(
        'wallet_id',
        'wallets',
        'id',
        'CASCADE',
        'CASCADE'
    );

    $this->forge->createTable('wallet_transactions');
}

public function down()
{
    $this->forge->dropTable('wallet_transactions');
}
}
