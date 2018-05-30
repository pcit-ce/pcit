<?php

declare(strict_types=1);

namespace App\Console\Khsci\Repo;

use App\Console\Khsci\KhsCICommand;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Env extends Command
{
    protected function configure(): void
    {
        $this->setName('env');
        $this->setDescription('Show or modify build environment variables');

        $this->addArgument(
            'type',
            InputArgument::REQUIRED,
            'type'
        );

        $this->addUsage('

khsci env list [OPTIONS]
khsci env set NAME VALUE [OPTIONS]
khsci env unset [NAMES..]      
');
        $this->addArgument('name', InputArgument::IS_ARRAY, 'name or value');

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getRepoOptionArray());

        $this->addOption(
            'public',
            'p',
            InputOption::VALUE_NONE,
            'Make new values public');
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
        $type = $input->getArgument('type');

        $argument = $input->getArgument('name');

        $repo = $input->getOption('repo');

        if (!$repo) {
            throw new Exception('Please set repo by -r option', 500);
        }

        switch ($type) {
            case 'list':
                $return = KhsCICommand::HttpGet(
                    $input,
                    'repo/'.$repo.'/env_vars',
                    null,
                    true);
                break;

            case 'set':
                list($name, $value) = $argument;

                $data = json_encode([
                    'env_var.name' => $name,
                    'env_var.value' => $value,
                    'env_var.public' => $input->hasOption('p'),
                ]);

                $return = KhsCICommand::HttpPost(
                    $input,
                    'repo/'.$repo.'/env_vars',
                    $data, true, true
                );

                break;
            case 'unset':

                foreach ($argument as $k) {
                }
                break;

            default:

                throw new Exception('command not found', 404);
        }

        $output->write($return);
    }
}
