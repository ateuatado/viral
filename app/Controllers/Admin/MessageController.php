<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignAssetModel;

class MessageController extends BaseController
{
    protected CampaignModel $campaignModel;
    protected CampaignAssetModel $assetModel;

    public function __construct()
    {
        $this->campaignModel = new CampaignModel();
        $this->assetModel = new CampaignAssetModel();
    }

    public function editor(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) return redirect()->to('/admin/campaigns')->with('error', 'Campanha não encontrada.');

        $chatMessages = $campaign['chat_messages'];
        if (is_string($chatMessages)) $chatMessages = json_decode($chatMessages, true);

        $data = [
            'campaign' => $campaign,
            'chatMessages' => $chatMessages ?: [],
            'assets' => $this->assetModel->getByCampaign($campaignId),
        ];
        return view('admin/messages/editor', $data);
    }

    public function save(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) return $this->response->setJSON(['error' => 'Campanha não encontrada'])->setStatusCode(404);

        $payload = $this->request->getJSON(true);
        $chatMessages = $payload['chat_messages'] ?? [];
        if (!is_array($chatMessages)) $chatMessages = [];

        $this->campaignModel->skipValidation(true)->update($campaignId, [
            'chat_messages'   => $chatMessages,
            'success_title'   => $payload['success_title'] ?? '',
            'success_message' => $payload['success_message'] ?? '',
            'owner_message'   => $payload['owner_message'] ?? '',
            'email_subject'   => $payload['email_subject'] ?? '',
            'email_body'      => $payload['email_body'] ?? '',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Mensagens salvas!']);
    }

    public function upload(string $campaignId)
    {
        $campaign = $this->campaignModel->find($campaignId);
        if (!$campaign) return $this->response->setJSON(['error' => 'Campanha não encontrada'])->setStatusCode(404);

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => 'Arquivo inválido'])->setStatusCode(400);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['error' => 'Tipo não permitido'])->setStatusCode(400);
        }

        if ($file->getSizeByUnit('mb') > 10) {
            return $this->response->setJSON(['error' => 'Arquivo muito grande (máx 10MB)'])->setStatusCode(400);
        }

        helper('viral');
        $campaignDir = FCPATH . 'assets/uploads/campaigns/' . $campaignId;
        if (!is_dir($campaignDir)) mkdir($campaignDir, 0755, true);

        $newName = $file->getRandomName();
        $file->move($campaignDir, $newName);

        $type = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';

        $assetData = [
            'id' => generate_uuid(),
            'campaign_id' => $campaignId,
            'type' => $type,
            'original_name' => $file->getClientName(),
            'stored_name' => $newName,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->assetModel->insert($assetData);

        return $this->response->setJSON([
            'success' => true,
            'url' => '/assets/uploads/campaigns/' . $campaignId . '/' . $newName,
            'type' => $type,
            'name' => $file->getClientName(),
        ]);
    }
}
