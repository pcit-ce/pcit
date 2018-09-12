<?php

declare(strict_types=1);

namespace App\Console\TencentAI;

use KhsCI\Support\JSON;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChatCommand extends Command
{
    private static $ai;

    protected function configure(): void
    {
        $this->setName('chat');

        $this->setDescription(<<<'EOF'
The basic chat interface provides text-based basic chat capabilities that allow your application to quickly have machine chat capabilities with contextual semantic understanding.
EOF
        );

        $this->addArgument('question', null, 'User input chat content');

        $this->addArgument('session', null, 'Session ID (unique within application)', '0');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $args = array_values($input->getArguments());

        array_shift($args);

        $output_array = TencentAICommand::get()->nlp()->chat(...$args);

        $output->writeln(JSON::beautiful(json_encode($output_array, JSON_UNESCAPED_UNICODE)));
    }
}
