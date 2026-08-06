<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'unsigned'=>true,
                'auto_increment'=>true
            ],

            'user_id'=>[
                'type'=>'INT',
                'unsigned'=>true
            ],

            'message'=>[
                'type'=>'TEXT'
            ],

            'is_read'=>[
                'type'=>'BOOLEAN',
                'default'=>false
            ],

            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true
            ]
        ]);

        $this->forge->addKey('id',true);

        $this->forge->addForeignKey(
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}