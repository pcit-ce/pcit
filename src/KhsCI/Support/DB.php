<?php

declare(strict_types=1);

namespace KhsCI\Support;

class DB
{
    private static $pdo;

    public static function connect()
    {
        if (!(self::$pdo instanceof \PDO)) {
            $mysql_host = Env::get('MYSQL_HOST');
            $mysql_port = Env::get('MYSQL_PORT');
            $mysql_username = Env::get('MYSQL_USERNAME');
            $mysql_password = Env::get('MYSQL_PASSWORD');
            $mysql_dbname = Env::get('MYSQL_DBNAME');

            $dsn = 'mysql:host='.$mysql_host.';port='.$mysql_port.';dbname='.$mysql_dbname;

            self::$pdo = new \PDO($dsn, $mysql_username, $mysql_password);
        }

        return self::$pdo;
    }
}
