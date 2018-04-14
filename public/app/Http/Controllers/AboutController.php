<?php

namespace App\Http\Controllers;

class AboutController
{
    public function __invoke()
    {
        echo "<br><br><h1 style='text-align: center'>The goal of KhsCI is to build CI/CD System by PHP Powered by Docker and Kubernetes.<h1>";
    }
}