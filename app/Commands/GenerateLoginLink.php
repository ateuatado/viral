<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PropagatorModel;

class GenerateLoginLink extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'user:login-link';
    protected $description = 'Generates or retrieves a direct user dashboard login link for a specific email address.';
    protected $usage       = 'user:login-link <email>';
    protected $arguments   = [
        'email' => 'The email address of the registered lead.',
    ];

    public function run(array $params)
    {
        if (empty($params[0])) {
            CLI::error('Please provide the lead email address.');
            CLI::write('Usage: php spark user:login-link <email>', 'yellow');
            return;
        }

        $email = trim($params[0]);
        CLI::write("Buscando link de login para: {$email}", 'cyan');

        $propagatorModel = new PropagatorModel();
        $propagator = $propagatorModel->where('email', $email)->first();

        if (!$propagator) {
            CLI::error("Nenhum lead encontrado com o e-mail: {$email}");
            CLI::write("Dica: Faça o cadastro deste e-mail primeiro pelo chat da Landing Page.", 'yellow');
            return;
        }

        // Se o lead existe mas não tem auth_token, cria um novo
        if (empty($propagator['auth_token'])) {
            $authToken = bin2hex(random_bytes(16));
            $propagatorModel->update($propagator['id'], [
                'auth_token' => $authToken
            ]);
            $propagator['auth_token'] = $authToken;
            CLI::write("Novo token de acesso gerado com sucesso.", 'green');
        }

        $loginUrl = base_url('login-token/' . $propagator['auth_token']);

        CLI::write("\n==================================================", 'yellow');
        CLI::write("Link de Acesso Direto à Área do Usuário:", 'green');
        CLI::write($loginUrl, 'white');
        CLI::write("==================================================\n", 'yellow');
    }
}
