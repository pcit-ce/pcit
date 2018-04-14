<?php

declare(strict_types=1);

namespace KhsCI\Support;

class Session
{
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

    public static function get(string $name)
    {
        session_start();
        $value = $_SESSION[$name] ?? null;
        session_write_close();

        return $value;
    }

    public static function all()
    {
        session_start();
        $value = $_SESSION;
        session_write_close();

        return $value;
    }

    public static function flush(): void
    {
        session_start();
        $_SESSION = [];
        session_write_close();
    }
}
