<?php

declare(strict_types=1);

namespace App\Console\KhsCI;

use Exception;
use KhsCI\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogoutCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('logout');

        $this->setDescription('Deletes the stored API token');

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());
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

            $output->writeln('Successfully logged out!');
        } else {
            throw new Exception('This User Not Found', 404);
        }
    }
}
