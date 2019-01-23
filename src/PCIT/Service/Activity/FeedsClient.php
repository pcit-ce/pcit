<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Activity;

use PCIT\Service\CICommon;

class FeedsClient
{
    use CICommon;

    /**
     * List feeds.
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/activity/feeds/#list-feeds
     */
    public function list()
    {
        return $this->curl->get($this->api_url.'/feeds');
    }
}
