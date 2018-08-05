# KhsCI

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/khsci.svg?style=social&label=Stars)](https://github.com/khs1994-php/khsci) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/khsci.svg)](https://packagist.org/packages/khs1994/khsci) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/khsci/all.svg)](https://github.com/khs1994-php/khsci/releases) [![Build Status](https://ci2.khs1994.com:10000/github/khs1994-php/khsci/status?branch=master)](https://ci.khs1994.com/github/khs1994-php/khsci) [![codecov](https://codecov.io/gh/khs1994-php/khsci/branch/master/graph/badge.svg)](https://codecov.io/gh/khs1994-php/khsci)

**国内首个基于 GitHub Checks API 使用 PHP 编写的运行于 Docker 之上的由 Tencent AI 驱动的开源云原生 CI/CD 系统**

* [Support Docs](https://docs.ci.khs1994.com)

* [Changelog](https://github.com/khs1994-php/khsci/blob/master/CHANGELOG.md)

* [Feedback](https://github.com/khs1994-php/khsci/issues)

* [API](https://ci.khs1994.com/api)

* [API Docs](https://api.ci.khs1994.com)

* [Donate](https://zan.khs1994.com)

* [KhsCI EE](https://github.com/khs1994-php/khsci/tree/master/docs#about-khsci-ce-and-ee)

## What is Continuous Integration (CI)?

* https://www.mindtheproduct.com/2016/02/what-the-hell-are-ci-cd-and-devops-a-cheatsheet-for-the-rest-of-us/

持续集成 (CI) 是一种软件开发实践，即团队开发成员经常集成他们的工作，而不是在开发周期结束时进行集成，通过每个成员每天至少集成一次，也就意味着每天可能会发生多次集成。每次集成都通过自动化的构建（包括编译，发布，自动化测试）来验证，从而尽早地发现集成错误。

持续集成 (CI) 的目标是通过以较小的增量进行 **开发** 和 **测试** 来构建更健康的软件。

![ci](https://user-images.githubusercontent.com/16733187/41330207-9416717c-6f04-11e8-961f-c606303e7bb5.jpg)

作为一个持续集成系统，**KhsCI** 通过自动 **构建** 和 **测试** 代码变更来支持团队的软件开发过程，为代码变更的构建状态提供即时的反馈。KhsCI 还可以通过管理部署和通知来自动化开发过程的其他部分。

当您提交代码到 Git，KhsCI 会进行构建，将您的 Git 仓库克隆到一个容器环境中，并执行一系列构建和测试代码的任务。如果其中一项或多项任务失败，则认为构建失败。如果没有任何任务失败，构建被认为通过，KhsCI 可以将您的代码部署到 Web 服务器、应用程序主机或容器集群中。

KhsCI 还可以使您的 **交付工作** 的其他部分实现自动化。这意味着您可以使用构建阶段相互依赖工作，设置通知，在构建之后准备部署以及执行许多其他任务。

## What is Cloud Native?

Cloud native computing uses an open source software stack to be:

1. **Containerized.** Each part (applications, processes, etc) is packaged in its own container. This facilitates reproducibility, transparency, and resource isolation.
2. **Dynamically orchestrated.** Containers are actively scheduled and managed to optimize resource utilization.
3. **Microservices oriented.** Applications are segmented into microservices. This significantly increases the overall agility and maintainability of applications.

## About KhsCI

**KhsCI** 由 **PHP 后端**（`Webhooks Server` + `Daemon CLI`） + **GitHub App** + **CLI** 三部分组成

* **Webhooks Server** 接收 Git 数据

* **Daemon CLI** 后端常驻 (守护) 程序，解析 Git 数据，在 Docker 单机或集群（Swarm、Kubernetes）中执行构建、测试、容器化部署的自动化过程。

* **CLI** 提供各种实用的功能，例如 命令行操作 GitHub，命令行调用 Tencent AI 开放能力，等

## 使用方法

* 点击 [KhsCI](https://github.com/apps/khsci) 安装 **KhsCI** `GitHub App`

* Git 仓库根目录包含 [`.khsci.yml`](https://github.com/khs1994-php/khsci/tree/master/yml_examples) 来配置 CI 规则（**需要私有部署 KhsCI**）。

```yaml
pipeline:
  
  php:
    imge: khs1994/php:7.2.8-fpm-alpine
    commands:
      - composer install
      - vendor/bin/phpunit
```

* 若想查看构建的聚合页面(详情，管理)，请登录 https://ci.khs1994.com/login

## Try Demo (KhsCI CE)

> KhsCI CE not support CI Feature(build test deployment) now, only support issues bot comments.As known as Public Cloud

[Install GitHub App](https://github.com/khs1994-php/khsci/tree/master/docs)

> You can [DONATE](https://zan.khs1994.com) KhsCI, MAYBE KhsCI CE will support free full CI features(build test deployment and more)

## Self-Hosting (KhsCI EE)

> Only KhsCI EE Support Full CI Features(build test deployment and more). As known as Private Cloud

* ~~PHP~~

* ~~MySQL~~

* ~~Redis~~

* ~~RabbitMQ~~

* **ONLY** need [Docker](https://github.com/yeasy/docker_practice/tree/master/install) and [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp) and [Website SSL/TLS Certificates](https://github.com/Neilpang/acme.sh)

To install **KhsCI EE** in your host, simply:

```bash
# install khs1994-docker/lnmp

$ git clone --recursive https://github.com/khs1994-docker/lnmp.git ~/lnmp

$ composer create-project khs1994/khsci:dev-master ~/lnmp/app/khsci

$ cd lnmp

# read lnmp/khsci/README.md, then exec

$ ./lnmp-docker.sh khsci-up
```

在 GitHub [Settings > Developer settings > GitHub Apps](https://github.com/settings/apps) 注册一个 GitHub App， 填入相关信息，在你的 Git 仓库安装注册好的 GitHub App 即可。

更多信息请查看 https://github.com/khs1994-php/khsci/blob/master/docs/install/ee.md

## Support

![](https://user-images.githubusercontent.com/16733187/41222863-c610772e-6d9a-11e8-8847-27ac16c8fb54.jpg)

扫码关注 **KhsCI** 微信公众平台，寻求 **KhsCI** 团队支持。

## Projects for KhsCI

* [Docker PHP](https://github.com/khs1994-docker/libdocker)

* [Docker Registry PHP](https://github.com/khs1994-docker/libregistry)

* [Tencent AI CLI]()

* [Kubernetes PHP]()

* [Wechat PHP](https://github.com/khs1994-php/libwechat)

* [DingTalk PHP]()

* [GitHub CLI]()

* [Gitee CLI]()

* [GitLab CLI]()

* [Gogs CLI]()

## Thanks

* [PHP](https://www.php.net)

* [Docker](https://www.docker.com)

* [Kubernetes](https://kubernetes.io/)

* [Travis CI](https://travis-ci.com)

* [Drone CI](https://drone.io)

### Other CI/CD on GitHub

* https://github.com/topics/continuous-integration?l=php&o=desc&s=stars

* https://github.com/topics/continuous-integration?o=desc&s=stars
