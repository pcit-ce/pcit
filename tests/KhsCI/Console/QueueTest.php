<?php

declare(strict_types=1);

namespace KhsCI\Tests\Console;

use App\Console\Queue;
use Exception;
use KhsCI\Tests\KhsCITestCase;

class QueueTest extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testSaveLog(): void
    {
        $queue = new Queue();

        $queue::setBuildKeyId(23);

        $queue::setUniqueId(0);

        $queue::saveLog();
    }
}
