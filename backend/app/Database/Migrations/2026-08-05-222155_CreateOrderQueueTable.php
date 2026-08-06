<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderQueueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'order_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending','processing','completed','failed'],
                'default' => 'pending',
            ],
            'attempts' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'available_at' => [
                'type' => 'DATETIME',
            ],
            'locked_by' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'locked_at' => [
                'type' => 'DATETIME',
                'null' => true,
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

        $this->forge->addForeignKey(
            'order_id',
            'orders',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('order_queue');
    }

    public function down()
    {
        $this->forge->dropTable('order_queue');
    }
}