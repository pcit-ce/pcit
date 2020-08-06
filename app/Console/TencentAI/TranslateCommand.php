<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranslateCommand extends Command
{
    private static $ai;

    public function configure(): void
    {
        $this->setName('translate');

        $this->setDescription(
            <<<'EOF'
Text translation interface provides automatic translation capabilities, can help you quickly complete a text translation, support for Chinese, English, german, French, Japanese, Korean, Spanish and Cantonese.
EOF
        );

        $this->addArgument('source_language', InputArgument::REQUIRED, 'Source language');
    }

    /**
     * @throws \Exception
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source_language = $input->getArgument('source_language');

        $output_array = TencentAICommand::get()->translate()->aILabText($source_language);

        $output->writeln(json_encode($output_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return 0;
    }
}
