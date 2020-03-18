<?php

declare(strict_types=1);

namespace PCIT\Runner\Tests\Events;

use PCIT\Runner\Events\Handler\EnvHandler;
use PHPUnit\Framework\TestCase;

class EnvHandlerTest extends TestCase
{
    public function test(): void
    {
        $result1 = (new EnvHandler())->handle(['k1=${K}', 'k2=v2'], ['K' => 'KV']);
        $result2 = (new EnvHandler())->handle(['k1=${K}', 'k2=v2'], ['K=KV']);

        $this->assertEquals($result1, $result2);
    }

    public function test_array2str(): void
    {
        $result = (new EnvHandler())->array2str('a');

        $this->assertEquals('a', $result);

        $result = (new EnvHandler())->array2str(['a', 'b']);

        $this->assertEquals('a,b', $result);
    }

    public function test_obj2array(): void
    {
        $result1 = (new EnvHandler())->obj2array(['k' => 'v=vv', 'k2' => 'v2==']);

        $result2 = (new EnvHandler())->obj2array(['k=v=vv', 'k2=v2==']);

        $this->assertEquals($result2, $result1);
    }

    public function test_array2obj(): void
    {
        $result1 = (new EnvHandler())->array2obj([
            'k=v=vv', 'k2=a,b,c', 'k3={"a": 1,"b": [a,b,c]}',
        ]);

        $result2 = (new EnvHandler())->array2obj([
            'k' => 'v=vv', 'k2' => 'a,b,c', 'k3' => '{"a": 1,"b": [a,b,c]}',
            ]);

        $this->assertEquals($result2, $result1);
    }
}
