<?php

namespace KhsCI\Tests\CLI;

use App\Console\BuildCommand;
use KhsCI\Tests\KhsCITestCase;

class BuildCommandTest extends KhsCITestCase
{
    private $build;

    protected function setUp()
    {
        $this->build = new BuildCommand();
    }

    /**
     * @group DON'TTEST
     */
    public function testCheckCIRoot()
    {
        $this->build->checkCIRoot();
    }

    /**
     * @group DON'TTEST
     */
    public function testSendEMail()
    {
        $this->build->config = json_encode(yaml_parse_file(__DIR__.'/../../../.khsci.yml'));


        $this->build->sendEMail();
    }
}
