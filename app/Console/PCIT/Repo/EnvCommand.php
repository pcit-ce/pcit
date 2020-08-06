<?php

declare(strict_types=1);

namespace App\Console\PCIT\Repo;

use App\Console\PCIT\PCITCommand;
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

pcit env list  [OPTIONS]
pcit env set   NAME VALUE [OPTIONS]
pcit env unset VAR_ID
pcit env get   VAR_ID
');
        $this->addArgument('name', InputArgument::IS_ARRAY, 'name or value');

        $this->addOption(...PCITCommand::getAPIEndpointOptionArray());

        $this->addOption(...PCITCommand::getGitTypeOptionArray());

        $this->addOption(...PCITCommand::getRepoOptionArray());

        $this->addOption(...PCITCommand::getRawOptionArray());

        $this->addOption(
            'public',
            'p',
            InputOption::VALUE_NONE,
            'Make new values public'
        );
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        $argument = $input->getArgument('name');

        $repo = PCITCommand::existsRepoOption($input);

        switch ($type) {
            case 'list':
                $return = PCITCommand::HttpGet(
                    $input,
                    'repo/'.$repo.'/env_vars',
                    null,
                    true
                );

                break;
            case 'set':
                list($name, $value) = $argument;

                $data = json_encode([
                    'env_var.name' => $name,
                    'env_var.value' => $value,
                    'env_var.public' => $input->hasOption('public'),
                ]);

                $return = PCITCommand::HttpPost(
                    $input,
                    'repo/'.$repo.'/env_vars',
                    $data,
                    true,
                    true
                );

                break;
            case 'unset':

                $return = PCITCommand::HttpDelete(
                    $input,
                    'repo/'.$repo.'/env_var/'.$argument[0],
                    null,
                    true
                );

                break;
            case 'get':

                $return = PCITCommand::HttpGet(
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

            return 0;
        }

        if ($input->getOption('raw')) {
            $output->write($return);

            return 0;
        }

        $table = new Table($output);

        $table->setHeaders(['id', 'name', 'value', 'public']);

        foreach (json_decode($return) as $item) {
            $row[] = [$item->id, $item->name, $item->value, $public = $item->public ? 'true' : 'false'];
        }

        if (!($row ?? null)) {
            $output->write('<info>env not set</info>');

            return 0;
        }

        $table->setRows($row);

        $table->render();

        return 0;
    }
}
