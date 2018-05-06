<?php

namespace KhsCI;

use Exception;
use Throwable;

class CIException extends Exception
{
    protected $unique_id;

    /**
     * CIException constructor.
     *
     * @param string         $unique_id
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $unique_id,
                                string $message = "",
                                int $code = 0,
                                Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->unique_id = $unique_id;
    }

    public function getUniqueId()
    {
        return $this->unique_id;
    }
}
