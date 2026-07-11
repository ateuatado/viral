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
        'id', 'campaign_id', 'token', 'parent_token', 'depth', 'name', 'phone', 'cpf', 'instagram', 'email', 'auth_token',
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
        'latitude' => '?float',
        'longitude' => '?float',
        'geo_accuracy' => '?float',
    ];

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)->first();
    }

    public function findByAuthToken(string $authToken): ?array
    {
        return $this->where('auth_token', $authToken)->first();
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

    public function calculateMaxDepthBelow(string $token, string $campaignId): int
    {
        $propagators = $this->where('campaign_id', $campaignId)->findAll();
        
        $childrenMap = [];
        foreach ($propagators as $p) {
            if (!empty($p['parent_token']) && (bool)$p['viralized']) {
                $childrenMap[$p['parent_token']][] = $p['token'];
            }
        }

        return $this->getTreeDepth($token, $childrenMap);
    }

    private function getTreeDepth(string $token, array &$childrenMap): int
    {
        if (!isset($childrenMap[$token]) || empty($childrenMap[$token])) {
            return 0;
        }
        $max = 0;
        foreach ($childrenMap[$token] as $childToken) {
            $d = 1 + $this->getTreeDepth($childToken, $childrenMap);
            if ($d > $max) {
                $max = $d;
            }
        }
        return $max;
    }
}
