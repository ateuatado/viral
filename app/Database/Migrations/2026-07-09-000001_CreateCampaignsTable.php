<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCampaignsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 100],
            'objective' => ['type' => 'TEXT', 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'history' => ['type' => 'JSON', 'null' => true],
            'structure' => ['type' => 'JSON', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'active', 'paused', 'ended'], 'default' => 'draft'],
            'config_geoloc' => ['type' => 'BOOLEAN', 'default' => false],
            'config_geoloc_mode' => ['type' => 'ENUM', 'constraint' => ['explicit', 'silent'], 'default' => 'explicit'],
            'offer_type' => ['type' => 'ENUM', 'constraint' => ['text', 'image', 'video', 'link', 'none'], 'default' => 'text'],
            'offer_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'offer_body' => ['type' => 'TEXT', 'null' => true],
            'offer_image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'offer_link_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'offer_link_text' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'offer_cta_text' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Compartilhe e ganhe!'],
            'success_message' => ['type' => 'TEXT', 'null' => true],
            'og_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'og_description' => ['type' => 'TEXT', 'null' => true],
            'og_image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'contact_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'contact_avatar' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'chat_messages' => ['type' => 'JSON'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'expires_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('campaigns');
    }

    public function down()
    {
        $this->forge->dropTable('campaigns');
    }
}
