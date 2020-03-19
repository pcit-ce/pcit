<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use JsonSchema\Constraints\BaseConstraint;
use PCIT\Runner\Client;
use PCIT\Runner\Events\Git;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class GitTest extends TestCase
{
    public function test(): void
    {
        $git = BaseConstraint::arrayToObjectRecursive(
            Yaml::parseFile(base_path().'.pcit/clone.yaml')['clone']['git']
        );

        $VAR = 'value';

        $client = new Client();
        $client->system_env = ["VAR=${VAR}"];

        $result = (new Git($git, null, $client))->parseGit();

        $this->assertEquals($result[2][2], "git3.t.khs1994.com:${VAR}");
    }
}
