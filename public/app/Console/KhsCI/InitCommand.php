<?php

declare(strict_types=1);

namespace App\Console\KhsCI;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('init');

        $this->setDescription('Generates a .khsci.yml');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $current_file = getcwd().'/.khsci.yml';

        if (file_exists($current_file)) {
            throw new Exception('.khsci.yml exists, skip', 500);
        }

        $content = <<<'EOF'
#
# @see https://github.com/khs1994-php/khsci/blob/master/docs/SUMMARY.md
#
        
clone:
  git:
    # image: plugins/git
    depth: 10
    # recursive: true
    # skip_verify: false
    # tags: false
    # submodule_override:
      # hello-world: https://github.com/octocat/hello-world.git

workspace:
  base: /app
  # path: .
  path: src

cache:
  directories:
  - vendor
  - .php_cs.cache

pipeline:
  #
  # This is phpunit demo
  #

  php:
    image: khs1994/php-fpm:${PHP_VERSION}
    pull: true
    environment:
      - a=1
    commands:
      - pwd
      - composer install -q
      - composer update -q
      - vendor/bin/phpunit
    when:
      # platform: linux/amd64
      # platform:  [ linux/*, windows/amd64 ]

      # status: changed
      # status:  [ failure, success ]

      # event: tag
      # event: [push, pull_request, tag, deployment]
      event: [push, pull_request, tag]

      # branch: master
      # branch: prefix/*
      # branch: [master, develop]
      # branch:
      #   include: [ master, release/* ]
      #   exclude: [ release/1.0.0, release/1.1.* ]

services:
  mysql:
    image: mysql:${MYSQL_VERSION}
    environment:
      - MYSQL_DATABASE=test
      - MYSQL_ROOT_PASSWORD=mytest
    # entrypoint: [ "mysqld" ]
    command: [ "--character-set-server=utf8mb4", "--default-authentication-plugin=mysql_native_password" ]

  # postgresql:
  #   image: postgres:${POSTGRESQL_VERSION}
  #   environment:
  #     - POSTGRES_USER=postgres
  #     - POSTGRES_DB=test

  redis:
    image: redis:${REDIS_VERSION}
    command: ["--bind", "0.0.0.0"]

  # mongodb:
  #   image: mongo:${MONGODB_VERSION}
  #   command: [ --smallfiles ]

matrix:
  PHP_VERSION:
    - 7.2.5-alpine3.7
    - 7.1.18-alpine
    # - 7.1.17-alpine3.4
    # - 7.0.30-alpine3.4
    # - 5.6.36-alpine3.4
  REDIS_VERSION:
    - 4.0.9-alpine
  MYSQL_VERSION:
    # - 8.0.11
    - 5.7.22
  MONGODB_VERSION:
    - 3.7.3
  POSTGRESQL_VERSION:
    - 10.3-alpine

# branches:
#   include: [ master, dev, feature/* ]
#   exclude: [ release/1.0.0, release/1.1.* ]

config:
  aliyun:
    docker_registry:
      # registry: git_repo_full_name
      # khs1994/wsl: khs1994-php/khsci

  tencent_cloud:
    docker_registry:
      # khs1994/wsl: khs1994-php/khsci      
EOF;

        file_put_contents($current_file, $content);

        return $output->write('.khsci.yml Generate Success');
    }
}
