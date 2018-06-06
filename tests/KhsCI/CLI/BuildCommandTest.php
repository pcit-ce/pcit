<?php

namespace KhsCI\Tests\CLI;


use App\Console\BuildCommand;
use KhsCI\Tests\KhsCITestCase;

class BuildCommandTest extends KhsCITestCase
{
    public $build;

    protected function setUp()
    {
        $this->build = new BuildCommand();
    }

    public function testAutoMerge()
    {

    }

}
