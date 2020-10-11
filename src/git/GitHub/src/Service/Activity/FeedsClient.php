<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Activity;

use PCIT\GPI\ServiceClientCommon;

class FeedsClient
{
    use ServiceClientCommon;

    /**
     * List feeds.
     *
     * @return mixed
     *
     * @see https://developer.github.com/v3/activity/feeds/#list-feeds
     */
    public function list()
    {
        return $this->curl->get($this->api_url.'/feeds');
    }
}
