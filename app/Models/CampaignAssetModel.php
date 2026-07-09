<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignAssetModel extends Model
{
    protected $table = 'campaign_assets';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'id', 'campaign_id', 'type', 'original_name',
        'stored_name', 'mime_type', 'size_bytes', 'created_at',
    ];
    protected $useTimestamps = false;

    public function getByCampaign(string $campaignId): array
    {
        return $this->where('campaign_id', $campaignId)->findAll();
    }
}
