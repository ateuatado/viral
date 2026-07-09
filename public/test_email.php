<?php
/**
 * test_email.php — Script de teste isolado para envio de e-mail e depuração SMTP no CodeIgniter 4.
 */

// Desativar limites de tempo
set_time_limit(60);
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Configuração de caminhos do CodeIgniter 4
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__);

// Carregar o Autoloader do Composer
require FCPATH . '../vendor/autoload.php';

// Definir constante do ambiente
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

// Inicializar o framework CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Carrega o serviço de e-mail
$email = \Config\Services::email();

// Configura o teste
$email->setTo('marcosantofoto@gmail.com');
$email->setSubject('Teste de Depuração SMTP — AWS SES');
$email->setMessage('<h1>Teste do Sistema de Viralização</h1><p>Esta é uma mensagem de teste enviada de forma isolada para diagnosticar a conexão SMTP com o Amazon SES.</p>');

echo "<h2>Iniciando tentativa de envio de e-mail...</h2>";

if ($email->send(false)) {
    echo "<h3 style='color:green;'>✓ E-mail enviado com sucesso!</h3>";
} else {
    echo "<h3 style='color:red;'>✗ Falha ao enviar o e-mail!</h3>";
}

// Imprime todo o log de depuração do SMTP
echo "<h4>Log do Depurador SMTP:</h4>";
echo "<pre style='background:#0f172a; color:#38bdf8; padding:1.5rem; border-radius:.5rem; overflow:auto; font-size:12px; line-height:1.5;'>";
echo htmlspecialchars($email->printDebugger(['headers', 'subject', 'body']));
echo "</pre>";
