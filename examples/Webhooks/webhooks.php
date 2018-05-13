<?php

use KhsCI\KhsCI;

// Start Webhooks Server

try {
    $khsci = new KhsCI();

    $khsci->webhooks->startGithubServer(null);

    // $khsci->webhooks->startGitHubAppServer(null);

    // get webhooks content

    $khsci->webhooks->getCache();

    // output is [type,event_type,$json_content]

} catch (Exception $e) {

}


