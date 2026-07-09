<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthTokenToPropagatorsTable extends Migration
{
    public function up()
    {
        $fields = [
            'auth_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'email',
            ],
        ];

        $this->forge->addColumn('propagators', $fields);
        $this->db->query("CREATE INDEX idx_propagators_auth_token ON propagators (auth_token)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX idx_propagators_auth_token ON propagators");
        $this->forge->dropColumn('propagators', 'auth_token');
    }
}
