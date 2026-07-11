<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMetaMessagesToCampaignsTable extends Migration
{
    public function up()
    {
        $fields = [
            'owner_meta_message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'owner_message',
            ],
            'owner_max_message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'owner_meta_message',
            ],
        ];
        $this->forge->addColumn('campaigns', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('campaigns', ['owner_meta_message', 'owner_max_message']);
    }
}
