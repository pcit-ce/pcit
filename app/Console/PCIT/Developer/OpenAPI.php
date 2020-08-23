<?php

declare(strict_types=1);

namespace App\Console\PCIT\Developer;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OpenAPI extends Command
{
    public function configure(): void
    {
        $this->setName('developer:openapi');
        $this->setDescription('Generate OpenAPI yaml file');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $controllers = require base_path('framework/storage/controllers.cache.php');

        foreach ($controllers as $controller) {
            $rc = new ReflectionClass('App\\Http\\Controllers\\'.$controller);

            $methods = $rc->getMethods();

            foreach ($methods as $method) {
                $attributes = $method->getAttributes();

                foreach ($attributes as $attr) {
                    if (\PCIT\Framework\Attributes\OpenAPI::class === $attr->getName()) {
                        $attr->newInstance()->getRaw();
                    }

                    return;
                }
            }
        }

        return 0;
    }
}
