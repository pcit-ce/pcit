<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components;

use PCIT\GPI\Webhooks\Context\Components\User\Account;

class Installation
{
    public int $id;

    public string $node_id;

    ///** @required */
    //public string $not_exists_in_json;

    public Account $account;
}
