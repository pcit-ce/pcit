<?php

declare(strict_types=1);

namespace App\Console\Khsci;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Token extends Command
{
    protected function configure(): void
    {
        $this->setName('token');

        $this->setDescription('Outputs the secret API token');

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list('api-endpoint' => $api_endpoint, 'git_type' => $git_type) = $input->getOptions();

        try {
            $output->writeln('Your access token is '.KhsCICommand::getToken($input, false));
        } catch (\Throwable $e) {
            throw new Exception('Please exec login first', 404);
        }
    }
}
