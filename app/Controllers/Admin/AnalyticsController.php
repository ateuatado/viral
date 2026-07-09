<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\PropagatorModel;
use App\Models\TrackingEventModel;

class AnalyticsController extends BaseController
{
    protected CampaignModel $campaignModel;
    protected PropagatorModel $propagatorModel;
    protected TrackingEventModel $eventModel;

    public function __construct()
    {
        helper('viral');
        $this->campaignModel  = new CampaignModel();
        $this->propagatorModel = new PropagatorModel();
        $this->eventModel      = new TrackingEventModel();
    }

    // ── Overview dashboard ──────────────────────────────────────────────
    public function overview(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) {
            return redirect()->to('/admin/campaigns')->with('error', 'Campanha não encontrada.');
        }

        $totalPropagators = $this->propagatorModel->countByCampaign($campaignId);
        $totalViralized   = $this->propagatorModel->countViralizedByCampaign($campaignId);
        $maxDepth         = $this->propagatorModel->getMaxDepth($campaignId);
        $geolocGranted    = $this->eventModel->countByType($campaignId, 'geoloc_granted');
        $pageViews        = $this->eventModel->countByType($campaignId, 'page_view');
        $linksGenerated   = $this->eventModel->countByType($campaignId, 'link_generated');

        $data = [
            'campaign'         => $campaign,
            'totalPropagators' => $totalPropagators,
            'totalViralized'   => $totalViralized,
            'viralizationRate' => $totalPropagators > 0
                ? round(($totalViralized / $totalPropagators) * 100, 1)
                : 0,
            'maxDepth'         => $maxDepth,
            'geolocGranted'    => $geolocGranted,
            'pageViews'        => $pageViews,
            'linksGenerated'   => $linksGenerated,
        ];

        return view('admin/analytics/overview', $data);
    }

    // ── Force-directed graph page ───────────────────────────────────────
    public function graph(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) {
            return redirect()->to('/admin/campaigns');
        }

        return view('admin/analytics/graph', ['campaign' => $campaign]);
    }

    // ── Geolocation map page ────────────────────────────────────────────
    public function map(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) {
            return redirect()->to('/admin/campaigns');
        }

        return view('admin/analytics/map', ['campaign' => $campaign]);
    }

    // ── JSON: propagators (nodes + links for D3) ────────────────────────
    public function propagatorsJson(string $campaignId)
    {
        $propagators = $this->propagatorModel->getCampaignPropagators($campaignId);

        $nodes    = [];
        $links    = [];
        $tokenMap = [];

        // Build children map for fast memory calculation of tree depths
        $childrenMap = [];
        foreach ($propagators as $p) {
            if (!empty($p['parent_token']) && (bool)$p['viralized']) {
                $childrenMap[$p['parent_token']][] = $p['token'];
            }
        }

        // Recursive tree depth calculator
        $getTreeDepth = function(string $token) use (&$childrenMap, &$getTreeDepth): int {
            if (!isset($childrenMap[$token]) || empty($childrenMap[$token])) {
                return 0;
            }
            $max = 0;
            foreach ($childrenMap[$token] as $childToken) {
                $d = 1 + $getTreeDepth($childToken);
                if ($d > $max) {
                    $max = $d;
                }
            }
            return $max;
        };

        foreach ($propagators as $p) {
            $tokenMap[$p['token']] = $p['id'];

            $maxDepthBelow = $getTreeDepth($p['token']);
            $discount = min(80, $maxDepthBelow * 10);

            $nodes[] = [
                'id'        => $p['id'],
                'token'     => $p['token'],
                'depth'     => (int) $p['depth'],
                'is_seed'   => (bool) $p['is_seed'],
                'viralized' => (bool) $p['viralized'],
                'latitude'  => $p['latitude']  ? (float) $p['latitude']  : null,
                'longitude' => $p['longitude'] ? (float) $p['longitude'] : null,
                'created_at' => $p['created_at'],
                'platform'  => $p['platform'],
                'ip'        => $p['ip'],
                'name'      => $p['name'],
                'phone'     => $p['phone'],
                'discount'  => $discount,
            ];

            if (!empty($p['parent_token']) && isset($tokenMap[$p['parent_token']])) {
                $links[] = [
                    'source' => $tokenMap[$p['parent_token']],
                    'target' => $p['id'],
                ];
            }
        }

        return $this->response->setJSON(['nodes' => $nodes, 'links' => $links]);
    }

    // ── JSON: tracking events ───────────────────────────────────────────
    public function eventsJson(string $campaignId)
    {
        $events = $this->eventModel->getCampaignEvents($campaignId);
        return $this->response->setJSON($events);
    }

    // ── CSV export ──────────────────────────────────────────────────────
    public function export(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) {
            return redirect()->to('/admin/campaigns');
        }

        $propagators = $this->propagatorModel->getCampaignPropagators($campaignId);

        // Build children map for fast memory calculation of tree depths
        $childrenMap = [];
        foreach ($propagators as $p) {
            if (!empty($p['parent_token']) && (bool)$p['viralized']) {
                $childrenMap[$p['parent_token']][] = $p['token'];
            }
        }

        // Recursive tree depth calculator
        $getTreeDepth = function(string $token) use (&$childrenMap, &$getTreeDepth): int {
            if (!isset($childrenMap[$token]) || empty($childrenMap[$token])) {
                return 0;
            }
            $max = 0;
            foreach ($childrenMap[$token] as $childToken) {
                $d = 1 + $getTreeDepth($childToken);
                if ($d > $max) {
                    $max = $d;
                }
            }
            return $max;
        };

        $filename = 'viral_' . ($campaign['slug'] ?? $campaignId) . '_' . date('Y-m-d') . '.csv';

        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Token', 'Token Pai', 'Nome', 'WhatsApp', 'Desconto (%)', 'Profundidade', 'IP', 'Plataforma',
            'Idioma', 'Resolução', 'Timezone', 'Latitude', 'Longitude',
            'Viralizado', 'Criado em',
        ]);

        foreach ($propagators as $p) {
            $maxDepthBelow = $getTreeDepth($p['token']);
            $discount = min(80, $maxDepthBelow * 10);

            fputcsv($output, [
                $p['token'],
                $p['parent_token'] ?? '-',
                $p['name'] ?? '-',
                $p['phone'] ?? '-',
                $discount . '%',
                $p['depth'],
                $p['ip'] ?? '-',
                $p['platform'] ?? '-',
                $p['language'] ?? '-',
                $p['screen_resolution'] ?? '-',
                $p['timezone'] ?? '-',
                $p['latitude'] ?? '-',
                $p['longitude'] ?? '-',
                $p['viralized'] ? 'Sim' : 'Não',
                $p['created_at'],
            ]);
        }

        fclose($output);

        return $this->response;
    }
}
