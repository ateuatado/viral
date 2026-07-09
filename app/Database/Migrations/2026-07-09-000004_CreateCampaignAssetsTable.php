<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaignAssetsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'campaign_id' => ['type' => 'CHAR', 'constraint' => 36],
            'type' => ['type' => 'ENUM', 'constraint' => ['image', 'video']],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'stored_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 100],
            'size_bytes' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('campaign_id', false, false, 'idx_campaign');
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('campaign_assets');
    }

    public function down()
    {
        $this->forge->dropTable('campaign_assets');
    }
}
