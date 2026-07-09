<?php

namespace App\Libraries;

use App\Models\PropagatorModel;

class TokenGenerator
{
    private PropagatorModel $propagatorModel;

    public function __construct()
    {
        $this->propagatorModel = new PropagatorModel();
    }

    public function generateUniqueToken(int $maxAttempts = 10): string
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            $token = bin2hex(random_bytes(6));
            if (!$this->propagatorModel->findByToken($token)) {
                return $token;
            }
        }
        throw new \RuntimeException('Failed to generate unique token after ' . $maxAttempts . ' attempts');
    }

    public function createSeedPropagator(string $campaignId): array
    {
        helper('viral');
        $id = generate_uuid();
        $token = $this->generateUniqueToken();

        $data = [
            'id' => $id,
            'campaign_id' => $campaignId,
            'token' => $token,
            'parent_token' => null,
            'depth' => 0,
            'is_seed' => true,
            'viralized' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->propagatorModel->insert($data);
        return $data;
    }

    public function createChildPropagator(string $campaignId, string $parentToken, array $trackingData = []): array
    {
        helper('viral');
        $parent = $this->propagatorModel->findByToken($parentToken);
        $depth = $parent ? ((int)$parent['depth'] + 1) : 0;

        $id = generate_uuid();
        $token = $this->generateUniqueToken();

        $data = array_merge([
            'id' => $id,
            'campaign_id' => $campaignId,
            'token' => $token,
            'parent_token' => $parentToken,
            'depth' => $depth,
            'is_seed' => false,
            'viralized' => false,
            'created_at' => date('Y-m-d H:i:s'),
        ], $trackingData);

        $this->propagatorModel->insert($data);
        return $data;
    }
}
