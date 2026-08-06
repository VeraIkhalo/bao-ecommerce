<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'unsigned'=>true,
                'auto_increment'=>true
            ],

            'processing_delay'=>[
                'type'=>'INT',
                'default'=>60
            ],

            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true
            ],

            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>true
            ]
        ]);

        $this->forge->addKey('id',true);

        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}