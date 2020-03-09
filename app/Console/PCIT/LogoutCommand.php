<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use PCIT\Framework\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogoutCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('logout');

        $this->setDescription('Deletes the stored API token');

        $this->addOption(...PCITCommand::getGitTypeOptionArray());

        $this->addOption(...PCITCommand::getAPIEndpointOptionArray());
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file_name = PCITCommand::getConfigFileName();

        ['git_type' => $git_type, 'api-endpoint' => $api_endpoint] = $input->getOptions();

        if (is_file($file_name)) {
            $tokenContent = json_decode(file_get_contents($file_name), true);

            unset($tokenContent['endpoints'][$api_endpoint][$git_type]);

            file_put_contents($file_name, JSON::beautiful(json_encode($tokenContent)));

            $output->writeln('<info>Successfully logged out!</info>');
        } else {
            $output->write('<error>This User Not Found</error>>');
        }

        return 0;
    }
}
