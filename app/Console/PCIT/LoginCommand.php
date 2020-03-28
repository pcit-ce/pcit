<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use Curl\Curl;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoginCommand extends Command
{
    public function configure(): void
    {
        $this->setName('login');

        $this->setDescription('Authenticates against the API and stores the token');

        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Git username');

        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Git password or Personal-API-Tokens');

        $this->addOption(...PCITCommand::getGitTypeOptionArray());

        $this->addOption(...PCITCommand::getAPIEndpointOptionArray());
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list(
            'username' => $username,
            'password' => $password,
            'git-type' => $git_type,
            'api-endpoint' => $api_endpoint
            ) = $input->getOptions();

        $request = json_encode([
                'git-type' => $git_type,
                'username' => $username,
                'password' => $password,
            ]
        );

        $curl = new Curl();

        $result = $curl->post($api_endpoint.'/api/user/token', $request);

        $token = json_decode($result)->token ?? '';

        $http_return_code = $curl->getCode();

        if (200 !== $http_return_code or !$token) {
            throw new Exception('Incorrect username or password or git-type', $http_return_code);
        }

        $file_name = PCITCommand::getConfigFileName();

        if (file_exists($file_name)) {
            $tokenContent = json_decode(file_get_contents($file_name), true);

            $tokenContent['endpoints'][$api_endpoint][$git_type] = $token;

            file_put_contents($file_name, json_encode($tokenContent, JSON_PRETTY_PRINT));

            $output->writeln('<info>Login Success</info>');

            return 0;
        }

        file_put_contents($file_name,
            json_encode(
                [
                    'endpoints' => [
                        $api_endpoint => [
                            $git_type => $token,
                        ],
                    ],
                ], JSON_PRETTY_PRINT
            )
        );

        $output->writeln('<info>Login Success</info>');

        return 0;
    }
}
