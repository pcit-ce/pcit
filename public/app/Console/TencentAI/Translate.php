<?php

namespace App\Console\TencentAI;

use Exception;
use KhsCI\KhsCI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Translate extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    public function configure()
    {
        $this->setName('translate');

        $this->setDescription('Translate');

        $this->addArgument('source_language');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source_language = $input->getArgument('source_language');

        $khsci = new KhsCI();

        $output = $khsci->tencent_ai->translate()->aILabText($source_language);

        var_dump($output);

        // $output->writeln($output);
    }

}
