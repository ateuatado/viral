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
        $authToken = bin2hex(random_bytes(16));
        $tempPassword = 'Temp!' . substr(bin2hex(random_bytes(4)), 0, 6);

        // Mark as viralized and save contact info (resilient try/catch for auth_token column)
        try {
            $propagatorModel->update($propagatorId, [
                'viralized'    => true,
                'viralized_at' => date('Y-m-d H:i:s'),
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'auth_token'   => $authToken,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Auth token column update failed (probably migration not run on production). Falling back. Error: ' . $e->getMessage());
            // Fallback: update without auth_token
            $propagatorModel->update($propagatorId, [
                'viralized'    => true,
                'viralized_at' => date('Y-m-d H:i:s'),
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
            ]);
            $authToken = null;
        }

        // Create User account in Shield (only if authToken is available)
        if ($isFirstTime && $authToken) {
            try {
                $users = auth()->getProvider();
                $existingUser = $users->findByCredentials(['email' => $email]);
                if (!$existingUser) {
                    $userEntity = new \CodeIgniter\Shield\Entities\User([
                        'username' => $propagator['token'],
                        'email'    => $email,
                        'password' => $tempPassword,
                    ]);
                    
                    if ($users->save($userEntity)) {
                        // Add to 'user' group
                        $insertId = $users->getInsertID();
                        if ($insertId) {
                            $newUser = $users->findById($insertId);
                            if ($newUser) {
                                $newUser->addGroup('user');
                                $newUser->forcePasswordReset();
                            }
                        }
                    } else {
                        log_message('error', 'Shield User Validation Failed: ' . json_encode($users->errors()));
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Shield User Creation Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            }
        }

        // Get campaign for the share URL
        $campaign = $campaignModel->find($propagator['campaign_id']);
        $shareUrl = base_url('v/' . $campaign['slug'] . '/' . $propagator['token']);
        $loginUrl = $authToken ? base_url('login-token/' . $authToken) : $shareUrl;

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

                    <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                        <p style='margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #0f172a;'>
                            🔑 Acesso ao Painel Exclusivo de Leads
                        </p>
                        <p style='margin: 0 0 12px 0; font-size: 13px; color: #64748b; line-height: 1.5;'>
                            Criamos uma conta de usuário para você poder acompanhar quem entrou na sua rede em tempo real em um Grafo interativo sem expor nomes de terceiros. Acesse pelo botão de login rápido abaixo:
                        </p>
                        <div style='text-align: center; margin: 15px 0;'>
                            <a href='" . htmlspecialchars($loginUrl) . "' style='background-color: #0f172a; color: #ffffff; padding: 10px 20px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 14px; display: inline-block;'>
                                Entrar no Painel e Definir Senha
                            </a>
                        </div>
                        <p style='margin: 12px 0 0 0; font-size: 12px; color: #94a3b8;'>
                            E-mail de acesso: <strong>" . htmlspecialchars($email) . "</strong><br>
                            Senha temporária: <code style='background: #e2e8f0; padding: 2px 4px; border-radius: 3px; color: #475569;'>" . htmlspecialchars($tempPassword) . "</code>
                        </p>
                    </div>
                    
                    <p style='font-size: 15px; line-height: 1.6; color: #475569;'>
                        Use o link abaixo para compartilhar diretamente com seus amigos e subir o seu desconto:
                    </p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . htmlspecialchars($shareUrl) . "' style='background-color: #22c55e; color: #ffffff; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 15px; display: inline-block;'>
                            Compartilhar Link do WhatsApp
                        </a>
                    </div>
                    
                    <p style='font-size: 13px; color: #64748b; line-height: 1.5;'>
                        Link de indicação exclusivo:<br>
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
