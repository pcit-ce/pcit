<?php

declare(strict_types=1);

namespace KhsCI\Tests\CLI;

use App\Console\BuildCommand;
use KhsCI\Tests\KhsCITestCase;

class BuildCommandTest extends KhsCITestCase
{
    private $build;

    protected function setUp(): void
    {
        $this->build = new BuildCommand();
    }

    /**
     * @group DON'TTEST
     */
    public function testCheckCIRoot(): void
    {
        $this->build->checkCIRoot();
    }

    /**
     * @group DON'TTEST
     */
    public function testSendEMail(): void
    {
        $this->build->config = json_encode(yaml_parse_file(__DIR__.'/../../../.khsci.yml'));

        $this->build->sendEMail();
    }
}
