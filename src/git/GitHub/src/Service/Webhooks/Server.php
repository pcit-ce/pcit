<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Webhooks;

use PCIT\GPI\Service\Webhooks\ServerAbstract;

/**
 * @see https://developer.github.com/webhooks/#events
 */
class Server extends ServerAbstract
{
    /**
     * @var string
     */
    protected $git_type = 'github';

    /**
     * @return int
     */
    public function server()
    {
        $this->verify('X-Hub-Signature');

        $event_type = $this->getEventType('X-Github-Event');

        return $this->storeAfterVerify($event_type);
    }
}
