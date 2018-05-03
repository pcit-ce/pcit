<?php

declare(strict_types=1);

namespace CLI;

use KhsCI\Support\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate extends Command
{
    protected function configure(): void
    {
        $this->setName('migrate');
        $this->setDescription('Migrate database');

        $this->addArgument('sql_file', null, 'New database by SQL file');

        $this->addOption('all', null, null, 'New all database');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql_file = $input->getArgument('sql_file');

        if ($sql_file) {
            if (in_array($sql_file, $this->getSqlList())) {
                DB::statement(file_get_contents(__DIR__.'/../sql/'.$sql_file));
            } else {
                var_dump($this->getSqlList());
            }

            return;
        }

        if ($input->getOption('all')) {
            foreach ($this->getSqlList() as $file) {
                DB::statement(file_get_contents(__DIR__.'/../sql/'.$file));

                return;
            }
        }
    }

    private function getSqlList()
    {
        $array = scandir(__DIR__.'/../sql');

        $array = array_filter($array, function ($k) {
            if (in_array($k, ['.', '..'])) {
                return false;
            }

            $spl = new \SplFileInfo(__DIR__.'/../sql'.$k);

            $ext = $spl->getExtension();

            if ('sql' !== $ext) {
                return false;
            }

            return true;
        });

        return $array;
    }
}
