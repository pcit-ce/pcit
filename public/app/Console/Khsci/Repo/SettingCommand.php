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

class SettingCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('setting');

        $this->setDescription('Access repository settings');

        $this->addArgument(
            'key',
            InputArgument::OPTIONAL,
            'Setting name'
        );

        $this->addOption(
            'set',
            's',
            InputOption::VALUE_REQUIRED,
            'Set to given value'
        );

        $this->addOption(...KhsCICommand::getRepoOptionArray());

        $this->addOption(...KhsCICommand::getGitTypeOptionArray());

        $this->addOption(...KhsCICommand::getAPIEndpointOptionArray());
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|mixed|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = KhsCICommand::existsRepoOption($input);

        $key = $input->getArgument('key');

        $set_value = $input->getOption('set');

        if (null === $key) {
            // key 参数不存在，返回设置列表
            return $output->write(
                KhsCICommand::HttpGet($input, 'repo/'.$repo.'/settings', null, true)
            );
        }

        if (null === $set_value) {
            // 指定 key 但没有 --set，返回设置值
            return $output->write(
                KhsCICommand::HttpGet($input, 'repo/'.$repo.'/setting/'.$key, null, true)
            );
        }

        if (null === $set_value) {
            throw new Exception('Please specify the Setting value via the -s option (e.g. khsci setting KEY -s true)');
        }

        $data = json_encode([
            'setting.'.$key => $set_value,
        ]);

        return $output->write(
            KhsCICommand::HttpPatch($input, 'repo/'.$repo.'/setting/'.$key, $data, true, true)
        );
    }
}
