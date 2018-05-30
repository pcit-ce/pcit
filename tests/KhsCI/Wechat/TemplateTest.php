<?php

namespace KhsCI\Tests\Wechat;

use Exception;
use KhsCI\Tests\KhsCITestCase;

class TemplateTest extends KhsCITestCase
{
    /**
     * @group DON'TTEST
     *
     * @throws Exception
     */
    public function testSend()
    {
        $khsci = self::getTest();

        $response = $khsci->wechat_template_message->SendTemplateMessage(
            'Success',
            time(),
            'push',
            'khs1994-php/khsci',
            'master',
            'khs1994',
            'This Build is Success',
            'https://ci.khs1994.com'
        );

        $this->assertEquals(0, json_decode($response)->errcode);
    }
}
