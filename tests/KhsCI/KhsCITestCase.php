<?php

declare(strict_types=1);

namespace KhsCI\Tests;

use App\Console\Migrate;
use Dotenv\Dotenv;
use Exception;
use KhsCI\KhsCI;
use PHPUnit\Framework\TestCase;

class KhsCITestCase extends TestCase
{
    private static $test;

    /**
     * @return KhsCI
     *
     * @throws Exception
     */
    public static function getTest()
    {
        if (!(self::$test instanceof KhsCI)) {
            self::$test = new KhsCI();
        }

        return self::$test;
    }

    /**
     * KhsCITestCase constructor.
     *
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     *
     * @throws Exception
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if (file_exists(__DIR__.'/../../public/.env.testing')) {
            (new Dotenv(__DIR__.'/../../public', '.env.testing'))->load();
        }
        ob_start();
        Migrate::all();
        ob_end_clean();

        parent::__construct($name, $data, $dataName);
    }
}
