<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrackingEventsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'propagator_id' => ['type' => 'CHAR', 'constraint' => 36],
            'event_type' => ['type' => 'ENUM', 'constraint' => ['page_view', 'geoloc_granted', 'geoloc_denied', 'offer_viewed', 'offer_clicked', 'link_generated', 'link_copied', 'whatsapp_share', 'chat_started', 'chat_completed']],
            'metadata' => ['type' => 'JSON', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('propagator_id', false, false, 'idx_propagator');
        $this->forge->addKey('event_type', false, false, 'idx_type');
        $this->forge->addKey('created_at', false, false, 'idx_created');
        $this->forge->addForeignKey('propagator_id', 'propagators', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tracking_events');
    }

    public function down()
    {
        $this->forge->dropTable('tracking_events');
    }
}
