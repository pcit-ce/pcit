<?php

declare(strict_types=1);

use KhsCI\KhsCI;

try {
    // Start Webhooks Server
    $khsci = new KhsCI([], 'github');

    $khsci->webhooks->Server(null);

    // $khsci->webhooks->startGitHubAppServer(null);

    // get webhooks content
    $khsci->webhooks->getCache();

    // output is [type,event_type,$json_content]
} catch (Exception $e) {
}
