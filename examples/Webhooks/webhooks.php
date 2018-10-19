<?php

declare(strict_types=1);

use PCIT\PCIT;

try {
    // Start Webhooks Server
    $pcit = new PCIT([], 'github');

    $pcit->webhooks->Server(null);

    // $pcit->webhooks->startGitHubAppServer(null);

    // get webhooks content
    $pcit->webhooks->getCache();

    // output is [type,event_type,$json_content]
} catch (Exception $e) {
}
