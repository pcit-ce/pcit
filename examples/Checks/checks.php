<?php

use KhsCI\KhsCI;

try {

    // create checks
    $khsci = new KhsCI();

    $khsci->check_run->create();

    // update checks
    $khsci->check_run->update();

} catch (Exception $e) {

}
