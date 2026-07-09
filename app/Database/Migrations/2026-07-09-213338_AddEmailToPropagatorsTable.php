<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailToPropagatorsTable extends Migration
{
    public function up()
    {
        $fields = [
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'phone'
            ]
        ];
        $this->forge->addColumn('propagators', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('propagators', 'email');
    }
}
