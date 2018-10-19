<?php

declare(strict_types=1);
use PCIT\PCIT;

try {
    // create checks
    $pcit = new PCIT();

    $pcit->check_run->create();

    // update checks
    $pcit->check_run->update();
} catch (Exception $e) {
}
