<?php

declare(strict_types=1);

namespace KhsCI;

use Exception;

class CIException extends Exception
{
    protected $unique_id;

    protected $commit_id;

    protected $event_type;

    /**
     * CIException constructor.
     *
     * @param string|null $unique_id
     * @param string      $commit_id
     * @param string      $event_type
     * @param string      $message
     * @param int         $code
     */
    public function __construct(?string $unique_id,
                                ?string $commit_id,
                                ?string $event_type,
                                string $message = '',
                                int $code = 0)
    {
        parent::__construct($message, $code);

        $this->unique_id = $unique_id;
        $this->commit_id = $commit_id;
        $this->event_type = $event_type;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->unique_id;
    }

    /**
     * @return mixed
     */
    public function getCommitId()
    {
        return $this->commit_id;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->event_type;
    }
}
