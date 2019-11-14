<?php

declare(strict_types=1);

namespace PCIT\Framework\Support;

class Session
{
    /**
     * @param $value
     */
    public static function put(string $name, $value): void
    {
        session_start();
        $_SESSION[$name] = $value;
        session_write_close();
    }

    public static function forget(string $name): void
    {
        session_start();
        unset($_SESSION[$name]);
        session_write_close();
    }

    /**
     * @return string|null
     */
    public static function get(string $name)
    {
        session_start();
        $value = $_SESSION[$name] ?? null;
        session_write_close();

        return $value;
    }

    /**
     * @return bool
     */
    public static function has(string $name)
    {
        session_start();
        $result = $_SESSION[$name] ?? false;
        session_write_close();

        return !(false === $result);
    }

    /**
     * @return mixed
     */
    public static function all()
    {
        session_start();
        $value = $_SESSION;
        session_write_close();

        return $value;
    }

    /**
     * 清空 Session.
     */
    public static function flush(): void
    {
        session_start();
        $_SESSION = [];
        session_write_close();
    }

    /**
     * 取出之后删除原数据.
     *
     * @return bool|null
     */
    public static function pull(string $name)
    {
        $result = self::has($name);

        if ($result) {
            $result = self::get($name);
            self::forget($name);

            return $result;
        }

        return null;
    }
}
