<?php

declare(strict_types=1);

namespace KhsCI\Support;

use Exception;
use PDO;
use PDOException;

class DB
{
    /**
     * @var PDO
     */
    private static $pdo;

    private static $debug;

    /**
     * @return PDO
     *
     * @throws Exception
     */
    public static function connection()
    {
        if (!(self::$pdo instanceof PDO)) {
            $mysql_host = Env::get('CI_MYSQL_HOST', 'mysql');
            $mysql_port = Env::get('CI_MYSQL_PORT', 3306);
            $mysql_username = Env::get('CI_MYSQL_USERNAME', 'root');
            $mysql_password = Env::get('CI_MYSQL_PASSWORD', 'mytest');
            $mysql_dbname = Env::get('CI_MYSQL_DBNAME', 'test');

            $dsn = 'mysql:host='.$mysql_host.';port='.$mysql_port.';dbname='.$mysql_dbname;

            try {
                $pdo = new PDO($dsn, $mysql_username, $mysql_password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo = $pdo;
            } catch (PDOException $e) {
                throw new Exception(
                    'Can\'t connect mysql server, error message is '.$e->getMessage().'. error code '.$e->getCode(), 500);
            }
        }

        return self::$pdo;
    }

    public static function close(): void
    {
        self::$pdo = null;
    }

    /**
     * 执行原生 SELECT 语句.
     *
     * @param string $sql
     * @param array  $data
     * @param bool   $single
     *
     * @return array|string
     *
     * @throws Exception
     */
    public static function select(string $sql, ?array $data, bool $single = false)
    {
        $pdo = self::connection();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            self::setDebugInfo($stmt);
            $output = $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        if ($single) {
            if (1 === count($output)) {
                foreach ($output[0] as $k => $v) {
                    return $v;
                }
            } else {
                return null;
            }
        }

        return $output;
    }

    /**
     * 执行原生 INSERT 语句.
     *
     * @param string $sql
     * @param array  $data
     *
     * @return int
     *
     * @throws Exception
     */
    public static function insert(string $sql, array $data = [])
    {
        $pdo = self::connection();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            self::setDebugInfo($stmt);
            $last = (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $last;
    }

    /**
     * 执行原生 UPDATE 语句.
     *
     * @param string $sql
     * @param array  $data
     *
     * @return int 返回受影响的记录条数
     *
     * @throws Exception
     */
    public static function update(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * 执行原生 DELETE 语句.
     *
     * @param string $sql
     * @param array  $data
     *
     * @return int
     *
     * @throws Exception
     */
    public static function delete(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * @param string $sql
     * @param array  $data
     *
     * @return int
     *
     * @throws Exception
     */
    private static function common(string $sql, array $data = [])
    {
        $pdo = self::connection();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            self::setDebugInfo($stmt);
            $count = $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $count;
    }

    /**
     * 执行普通语句.
     *
     * @param string $sql
     *
     * @return int
     *
     * @throws Exception
     */
    public static function statement(string $sql)
    {
        return self::connection()->exec($sql);
    }

    /**
     * @throws Exception
     */
    public static function beginTransaction(): void
    {
        self::connection()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        self::connection()->beginTransaction();
    }

    /**
     * @throws Exception
     */
    public static function commit(): void
    {
        self::connection()->commit();
        self::connection()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * @throws Exception
     */
    public static function rollback(): void
    {
        self::connection()->rollBack();
    }

    public static function createUser(): void
    {
    }

    public static function deleteUser(): void
    {
    }

    private static function setDebugInfo(\PDOStatement $stmt): void
    {
        ob_start();
        $stmt->debugDumpParams();
        self::$debug = ob_get_clean();
    }

    public static function getDebugInfo()
    {
        return self::$debug;
    }
}
