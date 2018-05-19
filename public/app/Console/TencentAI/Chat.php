<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use KhsCI\KhsCI;
use KhsCI\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Chat extends Command
{
    private static $ai;

    /**
     * Chat constructor.
     *
     * @param null|string $name
     *
     * @throws \Exception
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        self::$ai = (new KhsCI())->tencent_ai;
    }

    protected function configure(): void
    {
        $this->setName('chat');

        $this->setDescription(<<<EOF
The basic chat interface provides text-based basic chat capabilities that allow your application to quickly have machine chat capabilities with contextual semantic understanding.
EOF
        );

        $this->addArgument('question', null, 'User input chat content');

        $this->addArgument('session', null, 'Session ID (unique within application)', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $args = array_values($input->getArguments());

        array_shift($args);

        $output_array = self::$ai->nlp()->chat(...$args);

        $output->writeln(JSON::beautiful(json_encode($output_array, JSON_UNESCAPED_UNICODE)));
    }
}
