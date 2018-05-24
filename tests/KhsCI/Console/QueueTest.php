<?php


namespace KhsCI\Tests\Console;

use App\Console\Queue;
use Exception;
use KhsCI\Tests\KhsCITestCase;

class QueueTest extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     * @throws Exception
     */
    public function testSaveLog()
    {
        $queue = new Queue();

        $queue::$build_key_id = 22;

        $queue::$unique_id = 0;

        $queue::saveLog();
    }
}
