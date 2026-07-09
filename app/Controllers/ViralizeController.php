<?php

namespace App\Controllers;

use App\Models\PropagatorModel;
use App\Models\TrackingEventModel;
use App\Models\CampaignModel;

class ViralizeController extends BaseController
{
    public function create()
    {
        $json = $this->request->getJSON(true);
        if (!$json) return $this->response->setJSON(['error' => 'Invalid data'])->setStatusCode(400);

        helper('viral');
        $propagatorModel = new PropagatorModel();
        $eventModel = new TrackingEventModel();
        $campaignModel = new CampaignModel();

        $propagatorId = $json['propagator_id'] ?? null;
        if (!$propagatorId) return $this->response->setJSON(['error' => 'Missing propagator_id'])->setStatusCode(400);

        $propagator = $propagatorModel->find($propagatorId);
        if (!$propagator) return $this->response->setJSON(['error' => 'Propagator not found'])->setStatusCode(404);

        $name = trim($json['name'] ?? '');
        $phone = trim($json['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            return $this->response->setJSON(['error' => 'Nome e WhatsApp são obrigatórios para resgatar.'])->setStatusCode(400);
        }

        // Mark as viralized and save contact info
        $propagatorModel->update($propagatorId, [
            'viralized' => true,
            'viralized_at' => date('Y-m-d H:i:s'),
            'name' => $name,
            'phone' => $phone,
        ]);

        // Get campaign for the share URL
        $campaign = $campaignModel->find($propagator['campaign_id']);
        $shareUrl = base_url('v/' . $campaign['slug'] . '/' . $propagator['token']);

        // Log event
        $eventModel->insert([
            'id' => generate_uuid(),
            'propagator_id' => $propagatorId,
            'event_type' => 'link_generated',
            'metadata' => ['share_url' => $shareUrl],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'share_url' => $shareUrl,
            'token' => $propagator['token'],
        ]);
    }
}
