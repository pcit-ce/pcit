<?php

declare(strict_types=1);

namespace KhsCI\Tests\WeChat;

use Exception;
use KhsCI\Support\Date;
use KhsCI\Tests\KhsCITestCase;

class TemplateTest extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testSend(): void
    {
        $khsci = self::getTest();

        $response = $khsci->wechat_template_message->sendTemplateMessage(
            'Success',
            Date::Int2ISO(time()),
            'push',
            'khs1994-php/khsci',
            'master',
            'khs1994',
            'This Build is Success',
            'Build Info',
            'https://ci.khs1994.com'
        );

        $this->assertEquals(0, json_decode($response)->errcode);
    }
}
