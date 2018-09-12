<?php

declare(strict_types=1);

namespace App\Console\KhsCI\Repo;

use App\Console\KhsCI\KhsCICommand;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EnvCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('env');
        $this->setDescription('Show or modify build environment variables');

        $this->addArgument(
            'type',
            InputArgument::REQUIRED,
            'type is one of <comment>list</comment> <comment>set</comment> <comment>unset</comment> or <comment>get</comment>'
        );

        $this->addUsage('

khsci env list  [OPTIONS]
khsci env set   NAME VALUE [OPTIONS]
khsci env unset VAR_ID
khsci env get   VAR_ID     
');
        $this->addArgument('name', InputArgument::IS_ARRAY, 'name or value');

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getRepoOptionArray());

        $this->addOption(...KhsCICommand::getRawOptionArray());

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

        $repo = KhsCICommand::existsRepoOption($input);

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
                    'env_var.public' => $input->hasOption('public'),
                ]);

                $return = KhsCICommand::HttpPost(
                    $input,
                    'repo/'.$repo.'/env_vars',
                    $data, true, true
                );

                break;
            case 'unset':

                $return = KhsCICommand::HttpDelete(
                    $input,
                    'repo/'.$repo.'/env_var/'.$argument[0],
                    null,
                    true
                );

                break;

            case 'get':

                $return = KhsCICommand::HttpGet(
                    $input,
                    'repo/'.$repo.'/env_var/'.$argument[0],
                    null,
                    true
                );

                break;

            default:

                throw new Exception('command not found', 404);
        }

        if ('list' !== $input->getArgument('type')) {
            $output->write('<info>Success</info>');

            return;
        }

        if ($input->getOption('raw')) {
            $output->write($return);

            return;
        }

        $table = new Table($output);

        $table->setHeaders(['id', 'name', 'value', 'public']);

        foreach (json_decode($return) as $item) {
            $row[] = [$item->id, $item->name, $item->value, $public = $item->public ? 'true' : 'false'];
        }

        if (!($row ?? null)) {
            $output->write('<info>env not set</info>');

            return;
        }

        $table->setRows($row);

        $table->render();
    }
}
