<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuccessMessageToCampaignsTable extends Migration
{
    public function up()
    {
        // 1. Adiciona a coluna success_message se ela não existir
        if (!$this->db->fieldExists('success_message', 'campaigns')) {
            $fields = [
                'success_message' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                    'after'      => 'offer_cta_text',
                ],
            ];
            $this->forge->addColumn('campaigns', $fields);
        }

        // 2. Modifica a coluna offer_type para garantir que suporta o tipo 'video'
        $fieldsUpdate = [
            'offer_type' => [
                'name'       => 'offer_type',
                'type'       => 'ENUM',
                'constraint' => ['text', 'image', 'video', 'link', 'none'],
                'default'    => 'text',
            ],
        ];
        $this->forge->modifyColumn('campaigns', $fieldsUpdate);
    }

    public function down()
    {
        // Remove a coluna success_message
        if ($this->db->fieldExists('success_message', 'campaigns')) {
            $this->forge->dropColumn('campaigns', 'success_message');
        }

        // Restaura a coluna offer_type ao enum anterior
        $fieldsRestore = [
            'offer_type' => [
                'name'       => 'offer_type',
                'type'       => 'ENUM',
                'constraint' => ['text', 'image', 'link', 'none'],
                'default'    => 'text',
            ],
        ];
        $this->forge->modifyColumn('campaigns', $fieldsRestore);
    }
}
