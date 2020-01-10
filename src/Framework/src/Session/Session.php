<?php

declare(strict_types=1);

namespace PCIT\Framework\Session;

class Session
{
    /**
     * @param $value
     */
    public function put(string $name, $value): void
    {
        session_start();
        $_SESSION[$name] = $value;
        session_write_close();
    }

    public function forget(string $name): void
    {
        session_start();
        unset($_SESSION[$name]);
        session_write_close();
    }

    /**
     * @return string|null
     */
    public function get(string $name)
    {
        session_start();
        $value = $_SESSION[$name] ?? null;
        session_write_close();

        return $value;
    }

    /**
     * @return bool
     */
    public function has(string $name)
    {
        session_start();
        $result = $_SESSION[$name] ?? false;
        session_write_close();

        return !(false === $result);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        session_start();
        $value = $_SESSION;
        session_write_close();

        return $value;
    }

    /**
     * 清空 Session.
     */
    public function flush(): void
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
    public function pull(string $name)
    {
        $result = $this->has($name);

        if ($result) {
            $result = $this->get($name);
            $this->forget($name);

            return $result;
        }

        return null;
    }
}
