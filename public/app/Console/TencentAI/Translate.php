<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Translate extends Command
{
    private static $ai;

    /**
     * Translate constructor.
     *
     * @param null|string $name
     *
     * @throws Exception
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        self::$ai = (new KhsCI())->tencent_ai;
    }

    public function configure(): void
    {
        $this->setName('translate');

        $this->setDescription(<<<EOF
Text translation interface provides automatic translation capabilities, can help you quickly complete a text translation, support for Chinese, English, german, French, Japanese, Korean, Spanish and Cantonese.
EOF
        );

        $this->addArgument('source_language');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source_language = $input->getArgument('source_language');

        $output_array = self::$ai->translate()->aILabText($source_language);

        $output->writeln(JSON::beautiful(json_encode($output_array, JSON_UNESCAPED_UNICODE)));
    }
}
