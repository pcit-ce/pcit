<?php

namespace KhsCI\Tests\CLI;

use App\Console\BuildCommand;
use KhsCI\Tests\KhsCITestCase;

class BuildCommandTest extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     */
    public function testCheckCIRoot()
    {
        $build = new BuildCommand();

        $build->checkCIRoot();
    }
}
