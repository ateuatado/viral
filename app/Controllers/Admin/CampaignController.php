<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\PropagatorModel;
use App\Libraries\TokenGenerator;

class CampaignController extends BaseController
{
    protected CampaignModel $campaignModel;
    protected PropagatorModel $propagatorModel;

    public function __construct()
    {
        helper('viral');
        $this->campaignModel = new CampaignModel();
        $this->propagatorModel = new PropagatorModel();
    }

    public function index()
    {
        $campaigns = $this->campaignModel->orderBy('created_at', 'DESC')->findAll();

        // Attach seed token and page view count to each campaign
        foreach ($campaigns as &$c) {
            $seed = $this->propagatorModel->where('campaign_id', $c['id'])
                ->where('is_seed', true)->first();
            $c['seed_token'] = $seed ? $seed['token'] : null;
        }

        $data = [
            'campaigns' => $campaigns,
        ];
        return view('admin/campaigns/index', $data);
    }

    public function create()
    {
        return view('admin/campaigns/create');
    }

    public function store()
    {
        $data = [
            'id' => generate_uuid(),
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug') ?: generate_slug($this->request->getPost('name')),
            'objective' => $this->request->getPost('objective'),
            'description' => $this->request->getPost('description'),
            'status' => 'draft',
            'config_geoloc' => (bool) $this->request->getPost('config_geoloc'),
            'config_geoloc_mode' => $this->request->getPost('config_geoloc_mode') ?: 'explicit',
            'offer_type' => $this->request->getPost('offer_type') ?: 'text',
            'offer_title' => $this->request->getPost('offer_title'),
            'offer_body' => $this->request->getPost('offer_body'),
            'offer_link_url' => $this->request->getPost('offer_link_url'),
            'offer_link_text' => $this->request->getPost('offer_link_text'),
            'offer_cta_text' => $this->request->getPost('offer_cta_text') ?: 'Compartilhe e ganhe!',
            'og_title' => $this->request->getPost('og_title'),
            'og_description' => $this->request->getPost('og_description'),
            'contact_name' => $this->request->getPost('contact_name'),
            'chat_messages' => [],
            'history' => [['action' => 'created', 'date' => date('Y-m-d H:i:s')]],
            'structure' => [],
        ];

        // Handle OG image upload
        $ogImage = $this->request->getFile('og_image');
        if ($ogImage && $ogImage->isValid() && !$ogImage->hasMoved()) {
            $campaignDir = FCPATH . 'assets/uploads/campaigns/' . $data['id'];
            if (!is_dir($campaignDir)) mkdir($campaignDir, 0755, true);
            $newName = 'og_' . $ogImage->getRandomName();
            $ogImage->move($campaignDir, $newName);
            $data['og_image'] = '/assets/uploads/campaigns/' . $data['id'] . '/' . $newName;
        }

        // Handle contact avatar upload
        $avatar = $this->request->getFile('contact_avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            $campaignDir = FCPATH . 'assets/uploads/campaigns/' . $data['id'];
            if (!is_dir($campaignDir)) mkdir($campaignDir, 0755, true);
            $newName = 'avatar_' . $avatar->getRandomName();
            $avatar->move($campaignDir, $newName);
            $data['contact_avatar'] = '/assets/uploads/campaigns/' . $data['id'] . '/' . $newName;
        }

        if (!$this->campaignModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->campaignModel->errors());
        }

        return redirect()->to('/admin/campaigns')->with('success', 'Campanha criada com sucesso!');
    }

    public function edit(string $id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) return redirect()->to('/admin/campaigns')->with('error', 'Campanha não encontrada.');

        // Decode JSON fields if they're strings
        if (is_string($campaign['chat_messages'])) $campaign['chat_messages'] = json_decode($campaign['chat_messages'], true);
        if (is_string($campaign['history'])) $campaign['history'] = json_decode($campaign['history'], true);
        if (is_string($campaign['structure'])) $campaign['structure'] = json_decode($campaign['structure'], true);

        $seedPropagator = $this->propagatorModel->where('campaign_id', $id)->where('is_seed', true)->first();

        $data = [
            'campaign' => $campaign,
            'seedToken' => $seedPropagator ? $seedPropagator['token'] : null,
            'totalPropagators' => $this->propagatorModel->countByCampaign($id),
        ];
        return view('admin/campaigns/edit', $data);
    }

    public function update(string $id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) return redirect()->to('/admin/campaigns')->with('error', 'Campanha não encontrada.');

        $data = [
            'id' => $id,
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug'),
            'objective' => $this->request->getPost('objective'),
            'description' => $this->request->getPost('description'),
            'config_geoloc' => (bool) $this->request->getPost('config_geoloc'),
            'config_geoloc_mode' => $this->request->getPost('config_geoloc_mode') ?: 'explicit',
            'offer_type' => $this->request->getPost('offer_type') ?: 'text',
            'offer_title' => $this->request->getPost('offer_title'),
            'offer_body' => $this->request->getPost('offer_body'),
            'offer_link_url' => $this->request->getPost('offer_link_url'),
            'offer_link_text' => $this->request->getPost('offer_link_text'),
            'offer_cta_text' => $this->request->getPost('offer_cta_text') ?: 'Compartilhe e ganhe!',
            'og_title' => $this->request->getPost('og_title'),
            'og_description' => $this->request->getPost('og_description'),
            'contact_name' => $this->request->getPost('contact_name'),
        ];

        // Handle OG image upload
        $ogImage = $this->request->getFile('og_image');
        if ($ogImage && $ogImage->isValid() && !$ogImage->hasMoved()) {
            $campaignDir = FCPATH . 'assets/uploads/campaigns/' . $id;
            if (!is_dir($campaignDir)) mkdir($campaignDir, 0755, true);
            $newName = 'og_' . $ogImage->getRandomName();
            $ogImage->move($campaignDir, $newName);
            $data['og_image'] = '/assets/uploads/campaigns/' . $id . '/' . $newName;
        }

        // Handle contact avatar upload
        $avatar = $this->request->getFile('contact_avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            $campaignDir = FCPATH . 'assets/uploads/campaigns/' . $id;
            if (!is_dir($campaignDir)) mkdir($campaignDir, 0755, true);
            $newName = 'avatar_' . $avatar->getRandomName();
            $avatar->move($campaignDir, $newName);
            $data['contact_avatar'] = '/assets/uploads/campaigns/' . $id . '/' . $newName;
        }

        $history = $campaign['history'] ?? [];
        $history[] = ['action' => 'updated', 'date' => date('Y-m-d H:i:s')];
        $data['history'] = $history;

        // Validação manual de slug único (ignorando o próprio registro)
        $existingSlug = $this->campaignModel->where('slug', $data['slug'])->where('id !=', $id)->first();
        if ($existingSlug) {
            return redirect()->back()->withInput()->with('error', 'Este slug já está em uso por outra campanha.');
        }

        if (!$this->campaignModel->skipValidation(true)->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->campaignModel->errors());
        }

        return redirect()->to('/admin/campaigns/' . $id . '/edit')->with('success', 'Campanha atualizada!');
    }

    public function delete(string $id)
    {
        $this->campaignModel->delete($id);
        return redirect()->to('/admin/campaigns')->with('success', 'Campanha excluída.');
    }

    public function toggleStatus(string $id)
    {
        $campaign = $this->campaignModel->find($id);
        if (!$campaign) return redirect()->back()->with('error', 'Campanha não encontrada.');

        $newStatus = match ($campaign['status']) {
            'draft' => 'active',
            'active' => 'paused',
            'paused' => 'active',
            'ended' => 'draft',
            default => 'draft',
        };

        // If activating for the first time, create seed propagator
        if ($newStatus === 'active') {
            $existingSeed = $this->propagatorModel->where('campaign_id', $id)->where('is_seed', true)->first();
            if (!$existingSeed) {
                $tokenGen = new TokenGenerator();
                $tokenGen->createSeedPropagator($id);
            }
        }

        $this->campaignModel->skipValidation(true)->update($id, ['status' => $newStatus]);
        return redirect()->back()->with('success', 'Status alterado para: ' . $newStatus);
    }
}
