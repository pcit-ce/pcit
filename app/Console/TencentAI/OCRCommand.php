<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OCRCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('ocr');
        $this->setDescription('OCR');
        $this->addArgument('image', InputArgument::REQUIRED, 'Image file name');
        $this->addOption('raw', '-r', InputOption::VALUE_NONE, 'Show raw output');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('raw')) {
            $output->writeln(
                json_encode(
                    TencentAICommand::get()->ocr()->general(
                        getcwd().'/'.$input->getArgument('image')
                    ),
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                )
            );

            return 0;
        }

        $list = TencentAICommand::get()->ocr()->general(getcwd().'/'.$input->getArgument('image'))['data']['item_list'];

        $string = null;

        foreach ($list as $k) {
            $string .= $k['itemstring'];
        }

        $output->writeln($string);

        return 0;
    }
}
