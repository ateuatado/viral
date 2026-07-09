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
        $email = trim($json['email'] ?? '');
        $phone = trim($json['phone'] ?? '');

        if (empty($name) || empty($email) || empty($phone)) {
            return $this->response->setJSON(['error' => 'Nome, E-mail e WhatsApp são obrigatórios para resgatar.'])->setStatusCode(400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['error' => 'Formato de e-mail inválido.'])->setStatusCode(400);
        }

        $isFirstTime = !(bool)$propagator['viralized'];

        // Mark as viralized and save contact info
        $propagatorModel->update($propagatorId, [
            'viralized' => true,
            'viralized_at' => date('Y-m-d H:i:s'),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        // Get campaign for the share URL
        $campaign = $campaignModel->find($propagator['campaign_id']);
        $shareUrl = base_url('v/' . $campaign['slug'] . '/' . $propagator['token']);

        // Send silent confirmation email on first viralization
        if ($isFirstTime) {
            try {
                $emailService = \Config\Services::email();
                $emailService->setTo($email);
                
                $campaignName = $campaign['name'] ?? 'Campanha';
                $emailService->setSubject('🎯 Corrida de Cupons: Seu link de desconto está ativo!');
                
                $message = "
                <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; background-color: #ffffff; color: #334155;'>
                    <h2 style='color: #0f172a; margin-top: 0;'>Olá, " . htmlspecialchars($name) . "!</h2>
                    <p style='font-size: 15px; line-height: 1.6; color: #475569;'>
                        Você se cadastrou com sucesso na Corrida de Cupons da campanha <strong>" . htmlspecialchars($campaignName) . "</strong>!
                    </p>
                    
                    <div style='background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                        <p style='margin: 0; font-size: 14px; font-weight: bold; color: #0f172a;'>
                            🎁 Desconto Inicial Conquistado: 10% OFF
                        </p>
                        <p style='margin: 8px 0 0 0; font-size: 13px; color: #64748b; line-height: 1.5;'>
                            Seu desconto de 10% já está reservado. A cada amigo que entrar pelo seu link e indicar outra pessoa, o seu desconto sobe mais 10% (limite de 80%!).
                        </p>
                    </div>
                    
                    <p style='font-size: 15px; line-height: 1.6; color: #475569;'>
                        Use o link abaixo para compartilhar diretamente, acompanhar quem entrou na sua rede e ver seu cupom de desconto em tempo real:
                    </p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . htmlspecialchars($shareUrl) . "' style='background-color: #22c55e; color: #ffffff; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 15px; display: inline-block;'>
                            Ver Meu Painel de Descontos
                        </a>
                    </div>
                    
                    <p style='font-size: 13px; color: #64748b; line-height: 1.5;'>
                        Ou copie e envie este link de indicação para seus amigos:<br>
                        <code style='background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 13px; word-break: break-all; display: inline-block; margin-top: 6px;'>" . htmlspecialchars($shareUrl) . "</code>
                    </p>
                    
                    <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
                    <p style='font-size: 11px; color: #94a3b8; text-align: center; margin: 0;'>
                        Este é um e-mail transacional automático do Studio James Webb. Não é necessário respondê-lo.
                    </p>
                </div>";
                
                $emailService->setMessage($message);
                $emailService->send();
            } catch (\Exception $e) {
                log_message('error', 'Email Send Error: ' . $e->getMessage());
            }
        }

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
