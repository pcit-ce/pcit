<?php

declare(strict_types=1);

namespace KhsCI\DB\Tests;

use App\Console\Migrate;
use Exception;
use KhsCI\Support\DB;
use KhsCI\Tests\KhsCITestCase;
use PHPUnit\DbUnit\TestCaseTrait;

/**
 * PHPUnit 要求在测试套件开始时所有数据库对象必须全部可用。
 *
 * 数据库、表、序列、触发器还有视图，必须全部在运行测试套件之前创建好。
 *
 * Class DBTest
 */
class DBTest extends KhsCITestCase
{
    use TestCaseTrait;

    /**
     * @throws Exception
     */
    public function testCreateDB(): void
    {
        ob_start();
        Migrate::all();
        ob_end_clean();

        $this->assertEquals(1, 1);
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getConnection()
    {
        return $this->createDefaultDBConnection(DB::connect(), 'test');
    }

    protected function getDataSet()
    {
        // return $this->createFlatXMLDataSet(__DIR__.'/db_flat.xml');

        // return $this->createXMLDataSet(__DIR__.'/db.xml');

        return $this->createArrayDataSet(
            [
                'builds' => [
                    [
                        'id' => 1,
                    ],
                    [
                        'id' => 2,
                    ],
                ],
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function test(): void
    {
        // 对表中数据行的数量作出断言
        $this->assertEquals(2, $this->getConnection()->getRowCount('builds'));

        $queryTable = $this->getConnection()->createQueryTable('builds', 'SELECT id FROM builds');

        $exceptedTable = $this->createArrayDataSet([
            'builds' => [
                [
                    'id' => 1,
                ], [
                    'id' => 2,
                ],
            ],
        ])->getTable('builds');

        // 对结果断言
        $this->assertTablesEqual($exceptedTable, $queryTable);

        // $this->assertDataSetsEqual();

        // $this->assertTableContains();
    }

    /**
     * 测试的前提条件.
     *
     * @requires PHP 7.3-dev|7.2.5
     * 任何 PHP 版本标识符
     *
     * @requires PHPUnit 7.0
     * 任何 PHPUnit 版本标识符
     *
     * @requires OS Linux|WIN32|WINNT
     * 用来对 PHP_OS 进行匹配的正则表达式
     *
     * @requires function curl_init
     * 任何对 function_exists 而言有效的参数
     *
     * @requires extension redis 2.2.0
     * 任何扩展模块名，可以附带有版本标识符
     *
     * @requires extension mysqli
     */
    public function testRequire(): void
    {
        $this->assertEquals(0, 0);
    }

    /**
     * 跳过测试.
     */
    public function testSkip(): void
    {
        $this->markTestSkipped();
    }

    /**
     * 暂未完成的测试.
     */
    public function testInComplete(): void
    {
        $this->markTestIncomplete();
    }
}
