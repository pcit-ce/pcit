<?php

declare(strict_types=1);

namespace PCIT\Support;

class Session
{
    /**
     * @param string $name
     * @param        $value
     */
    public static function put(string $name, $value): void
    {
        session_start();
        $_SESSION[$name] = $value;
        session_write_close();
    }

    /**
     * @param string $name
     */
    public static function forget(string $name): void
    {
        session_start();
        unset($_SESSION[$name]);
        session_write_close();
    }

    /**
     * @param string $name
     *
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
     * @param string $name
     *
     * @return bool
     */
    public static function has(string $name)
    {
        session_start();
        $output = $_SESSION[$name] ?? false;
        session_write_close();

        return !(false === $output);
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
     * @param string $name
     *
     * @return bool|null
     */
    public static function pull(string $name)
    {
        $output = self::has($name);

        if ($output) {
            $output = self::get($name);
            self::forget($name);

            return $output;
        }

        return null;
    }
}
