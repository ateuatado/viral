<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\PropagatorModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $campaignModel   = new CampaignModel();
        $propagatorModel = new PropagatorModel();

        $totalAccesses   = $propagatorModel->countAll();
        $totalViralized  = $propagatorModel->where('viralized', true)->countAllResults();

        $data = [
            'totalCampaigns'   => $campaignModel->countAll(),
            'activeCampaigns'  => $campaignModel->where('status', 'active')->countAllResults(),
            'totalAccesses'    => $totalAccesses,
            'viralizationRate' => $totalAccesses > 0
                ? round(($totalViralized / $totalAccesses) * 100, 1)
                : 0,
            'recentCampaigns'  => $campaignModel
                ->orderBy('created_at', 'DESC')
                ->findAll(10),
        ];

        return view('admin/dashboard', $data);
    }
}
