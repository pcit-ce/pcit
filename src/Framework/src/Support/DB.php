<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

use Closure;
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
     */
    public static function connection()
    {
        if (!(self::$pdo instanceof PDO)) {
            $mysql_host = config('database.connections.mysql.host');
            $mysql_port = config('database.connections.mysql.port');
            $mysql_username = config('database.connections.mysql.username');
            $mysql_password = config('database.connections.mysql.password');
            $mysql_dbname = config('database.connections.mysql.database');

            $dsn = 'mysql:host='.$mysql_host.';port='.$mysql_port.';dbname='.$mysql_dbname;

            try {
                $pdo = new PDO($dsn, $mysql_username, $mysql_password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$pdo = $pdo;
            } catch (PDOException $e) {
                if (1049 === $e->getCode()) {
                    // 当数据库不存在时，尝试新建数据库
                    try {
                        $dsn = sprintf(
                            'mysql:host=%s;port=%s;dbname=%s',
                            $mysql_host,
                            $mysql_port,
                            'mysql'
                        );
                        $pdo = new PDO($dsn, $mysql_username, $mysql_password);

                        $pdo->exec('create database '.$mysql_dbname);

                        return self::connection();
                    } catch (PDOException $e) {
                        if (\PHP_SAPI === 'cli') {
                            die('[error] [PCIT] database not exists');
                        }

                        throw new Exception('database not exists', 500);
                    }
                }

                $errorCode = 500;

                if (2002 === $e->getCode()) {
                    if (\PHP_SAPI === 'cli') {
                        die('[error] PCIT can\'t connect DB Server'."\n");
                    }
                }

                throw new Exception('PCIT can\'t connect mysql server, error message is '.$e->getMessage().'. error code '.$e->getCode(), $errorCode);
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
     * @param array $data
     *
     * @return array|string
     */
    public static function select(string $sql, ?array $data, bool $single = false)
    {
        $pdo = self::connection();

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            self::setDebugInfo($stmt);
            $result = $stmt->fetchAll();
            // $stmt->closeCursor();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage().self::getDebugInfo(), 500);
        }

        if ($single) {
            if (1 === \count($result)) {
                foreach ($result[0] as $k => $v) {
                    return $v;
                }
            } else {
                return null;
            }
        }

        return $result;
    }

    /**
     * 执行原生 INSERT 语句.
     *
     * @return int
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
            throw new Exception($e->getMessage().self::getDebugInfo(), 500);
        }

        return $last;
    }

    /**
     * 执行原生 UPDATE 语句.
     *
     * @return int 返回受影响的记录条数
     */
    public static function update(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * 执行原生 DELETE 语句.
     *
     * @return int
     */
    public static function delete(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * @return int
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
            throw new Exception($e->getMessage().self::getDebugInfo(), 500);
        }

        return $count;
    }

    /**
     * 执行普通语句.
     *
     * @return int
     */
    public static function statement(string $sql)
    {
        return self::connection()->exec($sql);
    }

    public static function beginTransaction(): void
    {
        try {
            self::connection()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
            self::connection()->beginTransaction();
        } catch (\Throwable $e) {
        }
    }

    public static function transaction(Closure $callback): void
    {
        self::beginTransaction();
        \call_user_func($callback);
        self::commit();
    }

    public static function commit(): void
    {
        try {
            self::connection()->commit();
            self::connection()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
        } catch (\Throwable $e) {
        }
    }

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

    /**
     * @return PDO
     */
    public static function getPdo()
    {
        return self::connection();
    }
}
