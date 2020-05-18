<?php

declare(strict_types=1);

namespace PCIT\Coding\Service\Webhooks;

use PCIT\GPI\Service\Webhooks\ServerAbstract;

class Server extends ServerAbstract
{
    protected $git_type = 'coding';

    public function server()
    {
        $this->verify('X-Coding-Signature');

        $event_type = $this->getEventType('X-Coding-Event');

        return $this->storeAfterVerify($event_type);
    }
}
