<?php

namespace App\Controllers;

use App\Models\CampaignModel;
use App\Models\PropagatorModel;

class LandingController extends BaseController
{
    public function show(string $slug, string $token)
    {
        $campaignModel = new CampaignModel();
        $campaign = $campaignModel->findBySlug($slug);

        if (!$campaign || $campaign['status'] !== 'active') {
            return view('errors/html/error_404', ['message' => 'Campanha não encontrada ou inativa.']);
        }

        // Verify parent token exists
        $propagatorModel = new PropagatorModel();
        $parentPropagator = $propagatorModel->findByToken($token);
        if (!$parentPropagator || $parentPropagator['campaign_id'] !== $campaign['id']) {
            return view('errors/html/error_404', ['message' => 'Link inválido.']);
        }

        // Calculate parent discount info if viralized
        $parentMaxDepth = 0;
        $parentDiscount = 0;
        if ((bool)$parentPropagator['viralized']) {
            $parentMaxDepth = $propagatorModel->calculateMaxDepthBelow($token, $campaign['id']);
            $parentDiscount = min(80, $parentMaxDepth * 10);
        }

        $chatMessages = $campaign['chat_messages'];
        if (is_string($chatMessages)) $chatMessages = json_decode($chatMessages, true);

        $data = [
            'campaign' => $campaign,
            'parentToken' => $token,
            'chatMessages' => $chatMessages ?: [],
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
            'isParentViralized' => (bool)$parentPropagator['viralized'],
            'parentName' => $parentPropagator['name'],
            'parentDiscount' => $parentDiscount,
            'parentMaxDepth' => $parentMaxDepth,
        ];

        return view('landing/chat', $data);
    }
}
