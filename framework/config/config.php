<?php

declare(strict_types=1);

/**
 * only support github_app.
 *
 * new aliyun_docker_registry.json file
 *
 * {
 *   "aliyun_docker_registry_name（命名空间/仓库名称）": "github_repo_full_name",
 *   "khs1994/wsl": "khs1994-php/khsci"
 * }
 */
$aliyun_docker_registry_json_file = 'aliyun_docker_registry.json';

if (file_exists(__DIR__.'/'.$aliyun_docker_registry_json_file)) {
    $aliyun_docker_registry = json_decode(
        file_get_contents(__DIR__.'/'.$aliyun_docker_registry_json_file), true
    );
} else {
    $aliyun_docker_registry = [];
}
