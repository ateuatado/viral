<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailAndCustomMessagesToCampaignsTable extends Migration
{
    public function up()
    {
        $fields = [
            'success_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'offer_cta_text',
            ],
            'email_subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'success_message',
            ],
            'email_body' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'email_subject',
            ],
            'owner_message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'email_body',
            ],
        ];

        $this->forge->addColumn('campaigns', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('campaigns', ['success_title', 'email_subject', 'email_body', 'owner_message']);
    }
}
