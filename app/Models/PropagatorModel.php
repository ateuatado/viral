<?php

namespace App\Models;

use CodeIgniter\Model;

class PropagatorModel extends Model
{
    protected $table = 'propagators';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'id', 'campaign_id', 'token', 'parent_token', 'depth',
        'fingerprint', 'ip', 'user_agent', 'referrer',
        'language', 'screen_resolution', 'timezone', 'platform',
        'latitude', 'longitude', 'geo_accuracy',
        'is_seed', 'viralized', 'viralized_at', 'created_at',
    ];
    protected $useTimestamps = false;

    protected array $casts = [
        'depth' => 'int',
        'is_seed' => 'boolean',
        'viralized' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'geo_accuracy' => 'float',
    ];

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function getChildren(string $parentToken): array
    {
        return $this->where('parent_token', $parentToken)->findAll();
    }

    public function getCampaignPropagators(string $campaignId): array
    {
        return $this->where('campaign_id', $campaignId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    public function getCampaignSeeds(string $campaignId): array
    {
        return $this->where('campaign_id', $campaignId)
                    ->where('is_seed', true)
                    ->findAll();
    }

    public function getChain(string $token): array
    {
        $chain = [];
        $current = $this->findByToken($token);
        while ($current) {
            array_unshift($chain, $current);
            if (empty($current['parent_token'])) break;
            $current = $this->findByToken($current['parent_token']);
        }
        return $chain;
    }

    public function countByCampaign(string $campaignId): int
    {
        return $this->where('campaign_id', $campaignId)->countAllResults();
    }

    public function countViralizedByCampaign(string $campaignId): int
    {
        return $this->where('campaign_id', $campaignId)
                    ->where('viralized', true)
                    ->countAllResults();
    }

    public function getMaxDepth(string $campaignId): int
    {
        $result = $this->selectMax('depth')
                       ->where('campaign_id', $campaignId)
                       ->first();
        return (int) ($result['depth'] ?? 0);
    }
}
