<?php

//declare(strict_types=1);

namespace KhsCI\Support;

use Exception;
use PDO;
use PDOException;

class DB
{
    private static $pdo;

    /**
     * @return PDO
     * @throws Exception
     */
    public static function connect()
    {
        if (!(self::$pdo instanceof PDO)) {
            $mysql_host = Env::get('MYSQL_HOST');
            $mysql_port = Env::get('MYSQL_PORT');
            $mysql_username = Env::get('MYSQL_USERNAME');
            $mysql_password = Env::get('MYSQL_PASSWORD');
            $mysql_dbname = Env::get('MYSQL_DBNAME');

            $dsn = 'mysql:host='.$mysql_host.';port='.$mysql_port.';dbname='.$mysql_dbname;
            try {

                $pdo = new PDO($dsn, $mysql_username, $mysql_password);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                self::$pdo = $pdo;

            } catch (PDOException $e) {

                throw new Exception("Can't connect mysql server, mysql error code ".$e->getCode(), 500);
            }
        }

        return self::$pdo;
    }

    /**
     * 执行原生 SELECT 语句
     *
     * @param string $sql
     * @param array $data
     * @return array
     * @throws Exception
     */
    public static function select(string $sql, array $data = [])
    {
        $pdo = self::connect();

        try {
            $stmt = $pdo->prepare($sql);

            $stmt->execute($data);

            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $output = $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $output;
    }

    /**
     * 执行原生 INSERT 语句
     *
     * @param string $sql
     * @param array $data
     * @return string
     * @throws Exception
     */
    public static function insert(string $sql, array $data = [])
    {
        $pdo = self::connect();

        try {
            $stmt = $pdo->prepare($sql);

            $stmt->execute($data);

            $last = $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $last;

    }

    /**
     * 执行原生 UPDATE 语句
     *
     * @param string $sql
     * @param array $data
     * @return int         返回受影响的记录条数
     * @throws Exception
     */
    public static function update(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * 执行原生 DELETE 语句
     *
     * @param string $sql
     * @param array $data
     * @return int
     * @throws Exception
     */
    public static function delete(string $sql, array $data = [])
    {
        return self::common($sql, $data);
    }

    /**
     * @param string $sql
     * @param array $data
     * @return int
     * @throws Exception
     */
    private static function common(string $sql, array $data = [])
    {
        $pdo = self::connect();

        try {
            $stmt = $pdo->prepare($sql);

            $stmt->execute($data);

            $count = $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $count;
    }

    /**
     * 执行普通语句
     *
     * @param string $sql
     * @throws Exception
     */
    public static function statement(string $sql)
    {
        $pdo = self::connect();

        $pdo->exec($sql);
    }

    public static function createUser()
    {

    }

    public static function deleteUser()
    {

    }


}
