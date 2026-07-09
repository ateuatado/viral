<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropagatorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'campaign_id' => ['type' => 'CHAR', 'constraint' => 36],
            'token' => ['type' => 'VARCHAR', 'constraint' => 12],
            'parent_token' => ['type' => 'VARCHAR', 'constraint' => 12, 'null' => true],
            'depth' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'fingerprint' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'ip' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'referrer' => ['type' => 'TEXT', 'null' => true],
            'language' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'screen_resolution' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'timezone' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'platform' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'latitude' => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude' => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'geo_accuracy' => ['type' => 'FLOAT', 'null' => true],
            'is_seed' => ['type' => 'BOOLEAN', 'default' => false],
            'viralized' => ['type' => 'BOOLEAN', 'default' => false],
            'viralized_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token', 'uk_token');
        $this->forge->addKey('parent_token', false, false, 'idx_parent');
        $this->forge->addKey('campaign_id', false, false, 'idx_campaign');
        $this->forge->addKey(['campaign_id', 'depth'], false, false, 'idx_campaign_depth');
        $this->forge->addKey('fingerprint', false, false, 'idx_fingerprint');
        $this->forge->addForeignKey('campaign_id', 'campaigns', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('propagators');
    }

    public function down()
    {
        $this->forge->dropTable('propagators');
    }
}
