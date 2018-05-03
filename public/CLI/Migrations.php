<?php

namespace CLI;

use KhsCI\Support\DB;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Migrations extends Command
{
    protected function configure()
    {
        $this->setName('migration');
        $this->setDescription('Migration database');
        $this->addOption('all', null, null, 'new all database');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('all')) {
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

            foreach ($array as $file) {
                DB::statement(file_get_contents(__DIR__.'/../sql/'.$file));
            }

        }
    }
}
