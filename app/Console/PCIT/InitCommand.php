<?php

declare(strict_types=1);

namespace App\Console\PCIT;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('init');

        $this->setDescription('Generates a .pcit.yml');
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
        $current_file = getcwd().'/.pcit.yml';

        if (file_exists($current_file)) {
            echo '.pcit.yml exists, generate .pcit.yml.example'."\n\n";

            $current_file = getcwd().'/.pcit.yml.example';
        }

        $content = <<<'EOF'
# https://github.com/pcit-ce/pcit/blob/master/docs/SUMMARY.md
# https://ci.khs1994.com
# https://github.com/apps/pcit-ce

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
  # /app/pcit
  base: /app
  # path: .
  path: pcit

cache:
  directories:
  - vendor
  - .php_cs.cache

pipeline:

  # This is phpunit demo
  script:
    image: khs1994/php:${PHP_VERSION}-fpm-alpine
    # pull: true
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
      # matrix:
      #   - PHP_VERSION: 7.2.14
      #     REDIS_VERSION: 1.15.6
      #     MYSQL_VERSION: 5.7.22
      #     MONGODB_VERSION: 4.1.4
      #     POSTGRESQL_VERSION: 11.0

services:
  mysql:
    image: mysql:${MYSQL_VERSION}
    environment:
      - MYSQL_DATABASE=test
      - MYSQL_ROOT_PASSWORD=test
    # entrypoint: [ "mysqld" ]
    command: [ "--character-set-server=utf8mb4", "--default-authentication-plugin=mysql_native_password" ]

  # postgresql:
  #   image: postgres:${POSTGRESQL_VERSION}-alpine
  #   environment:
  #     - POSTGRES_USER=postgres
  #     - POSTGRES_DB=test

  redis:
    image: redis:${REDIS_VERSION}-alpine
    command: ["--bind", "0.0.0.0"]

  # mongodb:
  #   image: mongo:${MONGODB_VERSION}
  #   command: [ --smallfiles ]

matrix:
  PHP_VERSION:
    - 7.2.14
    - 7.1.23
  REDIS_VERSION:
    - 5.0.0
  MYSQL_VERSION:
    # - 8.0.11
    - 5.7.22
  MONGODB_VERSION:
    - 4.1.4
  POSTGRESQL_VERSION:
    - 11.0

# branches:
#   include: [ master, dev, feature/* ]
#   exclude: [ release/1.0.0, release/1.1.* ]

EOF;

        file_put_contents($current_file, $content);

        return $output->write($current_file.' Generate Success');
    }
}
