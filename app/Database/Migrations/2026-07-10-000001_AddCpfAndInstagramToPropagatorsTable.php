<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCpfAndInstagramToPropagatorsTable extends Migration
{
    public function up()
    {
        $fields = [
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => true,
                'after'      => 'phone',
            ],
            'instagram' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'cpf',
            ],
        ];

        $this->forge->addColumn('propagators', $fields);
        $this->db->query("CREATE INDEX idx_propagators_cpf ON propagators (cpf)");
    }

    public function down()
    {
        $this->db->query("DROP INDEX idx_propagators_cpf ON propagators");
        $this->forge->dropColumn('propagators', ['cpf', 'instagram']);
    }
}
