<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class TestEmail extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'email:test';
    protected $description = 'Tests email sending configuration.';
    protected $usage       = 'email:test <email_address>';
    protected $arguments   = [
        'email' => 'The destination email address to send the test message to.',
    ];

    public function run(array $params)
    {
        if (empty($params[0])) {
            CLI::error('Please provide a destination email address.');
            CLI::write('Usage: php spark email:test <email_address>', 'yellow');
            return;
        }

        $toEmail = $params[0];
        CLI::write("Testing email sending to: {$toEmail}", 'cyan');

        $email = Services::email();
        $email->setTo($toEmail);
        $email->setSubject('Teste de envio de E-mail do CodeIgniter');
        $email->setMessage('Se você está recebendo esta mensagem, o envio de e-mails configurado no sistema Viral está funcionando corretamente!');

        if ($email->send()) {
            CLI::write('Success! Email sent successfully.', 'green');
        } else {
            CLI::error('Failed! Could not send email.');
            CLI::write($email->printDebugger(['headers', 'subject', 'body']), 'red');
        }
    }
}
