<?php

namespace KhsCI\Support;

class Session
{
    public static function put(string $name, $value)
    {
        session_start();
        $_SESSION[$name] = $value;
        session_write_close();
    }

    public static function forget(string $name)
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

    public static function flush()
    {
        session_start();
        $_SESSION = [];
        session_write_close();
    }
}