<?php

use KhsCI\KhsCI;

try {

    // Start Webhooks Server
    $khsci = new KhsCI();

    $khsci->webhooks->startGitHubServer(null);

    // $khsci->webhooks->startGitHubAppServer(null);

    // get webhooks content
    $khsci->webhooks->getCache();

    // output is [type,event_type,$json_content]

} catch (Exception $e) {

}


