<?php

namespace App\Controllers;

use App\Models\PropagatorModel;
use App\Models\TrackingEventModel;
use App\Models\CampaignModel;
use App\Libraries\TokenGenerator;

class TrackingController extends BaseController
{
    public function store()
    {
        $json = $this->request->getJSON(true);
        if (!$json) return $this->response->setJSON(['error' => 'Invalid data'])->setStatusCode(400);

        helper('viral');
        $propagatorModel = new PropagatorModel();
        $eventModel = new TrackingEventModel();
        $campaignModel = new CampaignModel();

        $parentToken = $json['parent_token'] ?? null;
        $campaignSlug = $json['campaign_slug'] ?? null;

        if (!$parentToken || !$campaignSlug) {
            return $this->response->setJSON(['error' => 'Missing data'])->setStatusCode(400);
        }

        $campaign = $campaignModel->findBySlug($campaignSlug);
        if (!$campaign) return $this->response->setJSON(['error' => 'Campaign not found'])->setStatusCode(404);

        // Silent GeoIP geolocation if configured
        $lat = null;
        $lng = null;
        $geoAccuracy = null;

        if ((bool)$campaign['config_geoloc'] && $campaign['config_geoloc_mode'] === 'silent') {
            $ip = $this->request->getIPAddress();
            if ($ip === '127.0.0.1' || $ip === '::1') {
                // São Paulo local test coordinates with small random offsets
                $lat = -23.55052 + (mt_rand(-50, 50) / 1000);
                $lng = -46.633308 + (mt_rand(-50, 50) / 1000);
                $geoAccuracy = 5000;
            } else {
                try {
                    $client = \Config\Services::curlrequest();
                    $response = $client->get("http://ip-api.com/json/{$ip}", [
                        'timeout' => 2,
                    ]);
                    $geoData = json_decode($response->getBody(), true);
                    if (isset($geoData['status']) && $geoData['status'] === 'success') {
                        $lat = (float)$geoData['lat'];
                        $lng = (float)$geoData['lon'];
                        $geoAccuracy = 15000;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'GeoIP Lookup Exception: ' . $e->getMessage());
                }
            }
        }

        // Create visitor propagator (provisory, without own token for sharing yet)
        $tokenGen = new TokenGenerator();
        $trackingData = [
            'fingerprint' => $json['fingerprint'] ?? null,
            'ip' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'referrer' => $json['referrer'] ?? null,
            'language' => $json['language'] ?? null,
            'screen_resolution' => $json['screen_resolution'] ?? null,
            'timezone' => $json['timezone'] ?? null,
            'platform' => $json['platform'] ?? null,
            'latitude' => $lat,
            'longitude' => $lng,
            'geo_accuracy' => $geoAccuracy,
        ];

        $propagator = $tokenGen->createChildPropagator($campaign['id'], $parentToken, $trackingData);

        // Log page_view event
        $eventModel->insert([
            'id' => generate_uuid(),
            'propagator_id' => $propagator['id'],
            'event_type' => 'page_view',
            'metadata' => ['user_agent' => $trackingData['user_agent']],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'propagator_id' => $propagator['id'],
            'propagator_token' => $propagator['token'],
        ]);
    }

    public function storeGeo()
    {
        $json = $this->request->getJSON(true);
        if (!$json) return $this->response->setJSON(['error' => 'Invalid data'])->setStatusCode(400);

        helper('viral');
        $propagatorModel = new PropagatorModel();
        $eventModel = new TrackingEventModel();

        $propagatorId = $json['propagator_id'] ?? null;
        if (!$propagatorId) return $this->response->setJSON(['error' => 'Missing propagator_id'])->setStatusCode(400);

        $granted = $json['granted'] ?? false;

        if ($granted) {
            $propagatorModel->update($propagatorId, [
                'latitude' => $json['latitude'] ?? null,
                'longitude' => $json['longitude'] ?? null,
                'geo_accuracy' => $json['accuracy'] ?? null,
            ]);
            $eventType = 'geoloc_granted';
        } else {
            $eventType = 'geoloc_denied';
        }

        $eventModel->insert([
            'id' => generate_uuid(),
            'propagator_id' => $propagatorId,
            'event_type' => $eventType,
            'metadata' => $json,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }
}
