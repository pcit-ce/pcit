<?php

namespace App\Console\Khsci;


use Curl\Curl;
use Exception;
use KhsCI\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Login extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    public function configure()
    {
        $this->setName('login');

        $this->setDescription('Authenticates against the API and stores the token');

        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Git username');

        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Git password or Personal-API-Tokens');

        $this->addOption('git_type', 'g', InputOption::VALUE_OPTIONAL, 'Git Type', 'github');

        $this->addOption(
            'api-endpoint',
            'e',
            InputOption::VALUE_OPTIONAL,
            'KhsCI API server to talk to', Env::get('CI_HOST', 'https://ci.khs1994.com')
        );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getOption('username');

        $password = $input->getOption('password');

        $git_type = $input->getOption('git_type');

        $api_endpoint = $input->getOption('api-endpoint');

        if (!$git_type) {
            $git_type = 'github';
        }

        if (!$api_endpoint) {
            $api_endpoint = Env::get('CI_HOST', 'https://ci.khs1994.com');
        }

        $request = json_encode([
                'git_type' => $git_type,
                'username' => $username,
                'password' => $password,
            ]
        );

        $curl = new Curl();

        var_dump($curl->post($api_endpoint.'/api/user/token', $request));

    }
}
