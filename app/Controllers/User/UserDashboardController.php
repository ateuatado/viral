<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\PropagatorModel;
use App\Models\TrackingEventModel;
use App\Models\CampaignModel;

class UserDashboardController extends BaseController
{
    /**
     * Efetua o login automático via token de e-mail (auth_token).
     */
    public function loginByToken(string $token)
    {
        $propagatorModel = new PropagatorModel();
        $propagator = $propagatorModel->findByAuthToken($token);

        if (!$propagator) {
            return redirect()->to('/login')->with('error', 'Link de acesso inválido ou expirado.');
        }

        $email = $propagator['email'];
        $users = auth()->getProvider();
        $user = $users->findByCredentials(['email' => $email]);

        if (!$user) {
            // Fallback: se o registro do propagador existe mas a conta do Shield não, cria na hora
            try {
                $tempPassword = 'Temp!' . substr(bin2hex(random_bytes(4)), 0, 6);
                $userEntity = new \CodeIgniter\Shield\Entities\User([
                    'username' => $propagator['token'],
                    'email'    => $email,
                    'password' => $tempPassword,
                ]);
                $users->save($userEntity);

                $user = $users->findById($users->getInsertID());
                $user->addGroup('user');
                $user->forcePasswordReset();
            } catch (\Exception $e) {
                log_message('error', 'Shield Fallback User Creation Error: ' . $e->getMessage());
                return redirect()->to('/login')->with('error', 'Erro ao preparar sua conta de acesso.');
            }
        }

        // Efetua o login programático
        auth()->login($user);

        // Se for primeiro login, o Shield intercepta com force_reset
        return redirect()->to('/user/dashboard');
    }

    /**
     * Dashboard da área do usuário (propagador).
     */
    public function index()
    {
        // Garante que o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $user = auth()->user();
        $propagatorModel = new PropagatorModel();
        $eventModel = new TrackingEventModel();
        $campaignModel = new CampaignModel();

        // Encontra o propagador associado ao e-mail logado
        $propagator = $propagatorModel->where('email', $user->email)->first();

        if (!$propagator) {
            // Se o admin logar e tentar ver, ele não tem propagador. Deslogamos e mandamos pro login
            auth()->logout();
            return redirect()->to('/login')->with('error', 'Conta de usuário sem lead ativo correspondente.');
        }

        $campaign = $campaignModel->find($propagator['campaign_id']);

        // Estatísticas do link dele
        $clicksCount = $eventModel->where('propagator_id', $propagator['id'])
                                  ->where('event_type', 'page_view')
                                  ->countAllResults();

        // Desconto conquistado
        $maxDepth = $propagatorModel->calculateMaxDepthBelow($propagator['token'], $propagator['campaign_id']);
        $discount = min($maxDepth * 10, 80);

        // Conversões e visualizadores diretos
        $conversionsCount = $propagatorModel->where('parent_token', $propagator['token'])
                                            ->where('viralized', true)
                                            ->countAllResults();

        $visualizersCount = $propagatorModel->where('parent_token', $propagator['token'])
                                            ->where('viralized', false)
                                            ->countAllResults();

        // Link de indicação dele
        $shareUrl = base_url('v/' . ($campaign['slug'] ?? 'c') . '/' . $propagator['token']);

        $data = [
            'propagator'  => $propagator,
            'campaign'    => $campaign,
            'clicks'      => $clicksCount,
            'discount'    => $discount,
            'conversions' => $conversionsCount,
            'visualizers' => $visualizersCount,
            'share_url'   => $shareUrl,
        ];

        return view('user/dashboard', $data);
    }

    /**
     * API JSON do Grafo D3 - Retorna apenas a subárvore do propagador anonimizando descendentes.
     */
    public function networkJson()
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $user = auth()->user();
        $propagatorModel = new PropagatorModel();

        // Busca o propagador atual
        $currentUser = $propagatorModel->where('email', $user->email)->first();
        if (!$currentUser) {
            return $this->response->setJSON(['nodes' => [], 'links' => []]);
        }

        // Busca todos os propagadores da campanha
        $all = $propagatorModel->where('campaign_id', $currentUser['campaign_id'])->findAll();

        // Mapeia por Token para navegação rápida
        $byToken = [];
        $childrenByParent = [];
        foreach ($all as $node) {
            $byToken[$node['token']] = $node;
            $parent = $node['parent_token'];
            if ($parent) {
                $childrenByParent[$parent][] = $node['token'];
            }
        }

        // Coleta de forma recursiva apenas os tokens dos descendentes
        $mySubtreeTokens = [];
        $collectSubtree = function(string $token) use (&$collectSubtree, &$mySubtreeTokens, $childrenByParent) {
            $mySubtreeTokens[] = $token;
            if (isset($childrenByParent[$token])) {
                foreach ($childrenByParent[$token] as $childToken) {
                    $collectSubtree($childToken);
                }
            }
        };

        // Dispara a coleta a partir do nó do usuário atual
        $collectSubtree($currentUser['token']);

        $nodes = [];
        $links = [];

        // Monta a lista de nós com anonimização
        foreach ($mySubtreeTokens as $token) {
            if (!isset($byToken[$token])) continue;

            $node = $byToken[$token];
            $isMe = ($token === $currentUser['token']);

            // Se for o próprio lead logado, mostra dados reais dele
            if ($isMe) {
                $displayName = $node['name'] ?? 'Você';
                $emailDisplay = $node['email'] ?? '';
                $phoneDisplay = $node['phone'] ?? '';
            } else {
                // Caso contrário (descendentes), esconde por completo!
                $displayName = 'Lead ' . substr($token, 0, 6);
                $emailDisplay = 'Privado (LGPD)';
                $phoneDisplay = 'Privado (LGPD)';
            }

            // Calcula o desconto desse nó na árvore dele
            $nodeMaxDepth = $propagatorModel->calculateMaxDepthBelow($token, $currentUser['campaign_id']);
            $nodeDiscount = min($nodeMaxDepth * 10, 80);

            $nodes[] = [
                'id'          => $token,
                'name'        => $displayName,
                'email'       => $emailDisplay,
                'phone'       => $phoneDisplay,
                'is_me'       => $isMe,
                'viralized'   => (bool)$node['viralized'],
                'depth'       => (int)$node['depth'],
                'discount'    => $nodeDiscount,
                'visualizers' => $propagatorModel->where('parent_token', $token)->where('viralized', false)->countAllResults(),
            ];

            // Adiciona conexão de link se o pai estiver na nossa subárvore
            if ($node['parent_token'] && in_array($node['parent_token'], $mySubtreeTokens)) {
                $links[] = [
                    'source' => $node['parent_token'],
                    'target' => $token,
                ];
            }
        }

        return $this->response->setJSON([
            'nodes' => $nodes,
            'links' => $links,
        ]);
    }
}
