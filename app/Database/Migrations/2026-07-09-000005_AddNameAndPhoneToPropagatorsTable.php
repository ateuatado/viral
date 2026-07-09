<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNameAndPhoneToPropagatorsTable extends Migration
{
    public function up()
    {
        $fields = [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'depth'
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'name'
            ]
        ];
        $this->forge->addColumn('propagators', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('propagators', 'name');
        $this->forge->dropColumn('propagators', 'phone');
    }
}
