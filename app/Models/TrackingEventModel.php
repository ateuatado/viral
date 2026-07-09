<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackingEventModel extends Model
{
    protected $table = 'tracking_events';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'id', 'propagator_id', 'event_type', 'metadata', 'created_at',
    ];
    protected $useTimestamps = false;

    protected $casts = [
        'metadata' => 'json-array',
    ];

    public function getByPropagator(string $propagatorId): array
    {
        return $this->where('propagator_id', $propagatorId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function countByType(string $campaignId, string $eventType): int
    {
        return $this->join('propagators', 'propagators.id = tracking_events.propagator_id')
                    ->where('propagators.campaign_id', $campaignId)
                    ->where('tracking_events.event_type', $eventType)
                    ->countAllResults();
    }

    public function getCampaignEvents(string $campaignId): array
    {
        return $this->join('propagators', 'propagators.id = tracking_events.propagator_id')
                    ->where('propagators.campaign_id', $campaignId)
                    ->orderBy('tracking_events.created_at', 'DESC')
                    ->findAll();
    }
}
