<?php


namespace App\Console\Khsci;

use Exception;
use KhsCI\Support\Env;
use KhsCI\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Logout extends Command
{
    protected function configure()
    {
        $this->setName('logout');

        $this->setDescription('Deletes the stored API token');

        $this->addOption(
            'git_type',
            'g',
            InputOption::VALUE_OPTIONAL,
            'Git Type',
            'github'
        );

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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $file_name = KhsCICommand::getConfigFileName();

        ['git_type' => $git_type, 'api-endpoint' => $api_endpoint] = $input->getOptions();

        if (is_file($file_name)) {
            $array = json_decode(file_get_contents($file_name), true);

            unset($array['endpoints'][$api_endpoint][$git_type]);

            file_put_contents($file_name, JSON::beautiful(json_encode($array)));

        } else {

            throw new Exception('This User Not Found', 404);
        }
    }
}
