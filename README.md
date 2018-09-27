# PCIT (PCIT is not only a PHP CI TOOLKIT)

> 使用 PHP 编写的持续集成工具，支持 PHP、Java、Node 等 **全部** 开发语言。

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/pcit.svg?style=social&label=Stars)](https://github.com/khs1994-php/pcit) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/pcit.svg)](https://packagist.org/packages/khs1994/pcit) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/pcit/all.svg)](https://github.com/khs1994-php/pcit/releases) [![Build Status](https://ci2.khs1994.com:10000/github/khs1994-php/pcit/status?branch=master)](https://ci2.khs1994.com:10000/github/khs1994-php/pcit) [![codecov](https://codecov.io/gh/khs1994-php/pcit/branch/master/graph/badge.svg)](https://codecov.io/gh/khs1994-php/pcit) [![qqgroup-894134241](https://img.shields.io/badge/QQ%E7%BE%A4-894134241-blue.svg)](https://shang.qq.com/wpa/qunwpa?idkey=776defd7c271e9de70b9dfae855a34f11aada1fec9f27d22303dfffcb6d75e63)

**国内首个基于 GitHub Checks API 使用 PHP 编写的运行于 Docker 之上的由 Tencent AI 驱动的开源云原生 CI/CD 系统**

* [Support Docs](https://docs.ci.khs1994.com)

* [Changelog](https://github.com/khs1994-php/pcit/blob/master/CHANGELOG.md)

* [Feedback](https://github.com/khs1994-php/pcit/issues)

* [API](https://ci.khs1994.com/api)

* [API Docs](https://api.ci.khs1994.com)

* [Plugins](https://github.com/khs1994-php/pcit/tree/master/plugins)

* [PHP Docs](https://khs1994-php.github.io/pcit)

* [Donate](https://zan.khs1994.com)

* [PCIT EE](https://github.com/khs1994-php/pcit/tree/master/docs#about-pcit-ce-and-ee)

## 项目状态

积极开发中，部分描述或功能只是 **路线图** 中的一部分，可能并未实现，请点击 `Star` 或关注微博、微信、QQ群（见下方）保持对 PCIT 的关注。

## 什么是持续集成 Continuous Integration (CI)?

* https://www.mindtheproduct.com/2016/02/what-the-hell-are-ci-cd-and-devops-a-cheatsheet-for-the-rest-of-us/

持续集成 (CI) 是一种软件开发实践，即团队开发成员经常集成他们的工作，而不是在开发周期结束时进行集成，通过每个成员每天至少集成一次，也就意味着每天可能会发生多次集成。每次集成都通过自动化的构建（包括编译，发布，自动化测试）来验证，从而尽早地发现集成错误。

持续集成 (CI) 的目标是通过以较小的增量进行 **开发** 和 **测试** 来构建更健康的软件。

![ci](https://user-images.githubusercontent.com/16733187/41330207-9416717c-6f04-11e8-961f-c606303e7bb5.jpg)

作为一个持续集成系统，**PCIT** 通过自动 **构建** 和 **测试** 代码变更来支持团队的软件开发过程，为代码变更的构建状态提供即时的反馈。PCIT 还可以通过管理部署和通知来自动化开发过程的其他部分。

当您提交代码到 Git，PCIT 会进行构建，将您的 Git 仓库克隆到一个容器环境中，并执行一系列构建和测试代码的任务。如果其中一项或多项任务失败，则认为构建失败。如果没有任何任务失败，构建被认为通过，PCIT 可以将您的代码部署到 Web 服务器、应用程序主机或容器集群中。

PCIT 还可以使您的 **交付工作** 的其他部分实现自动化。这意味着您可以使用构建阶段相互依赖工作，设置通知，在构建之后准备部署以及执行许多其他任务。

## PCIT 架构

**PCIT** 由 **PHP 后端**（`Webhooks Server` + `Daemon CLI`） + **GitHub App** + **CLI** + **开放平台**（`插件`、`API`）四部分组成

* **Webhooks Server** 接收 Git 数据

* **Daemon CLI** 后端常驻 (守护) 程序，解析 Git 数据，在 Docker 单机或集群（Swarm、Kubernetes）中执行构建、测试、容器化部署的自动化过程。

* **CLI** 提供各种实用的功能，例如 命令行操作 GitHub，命令行调用 Tencent AI 开放能力，等

* **开放平台** 包含用于功能扩展的 **插件** 和 **RESTFul API**，与开发者一道构建 PCIT 生态系统

## 使用方法（使用全部功能需要私有部署 PCIT EE）

* 点击 [PCIT-CE](https://github.com/apps/pcit-ce) 安装 **PCIT CE** `GitHub App`

* Git 仓库根目录包含 [`.pcit.yml`](https://github.com/khs1994-php/pcit/tree/master/yml_examples) 来配置 CI 规则（**需要私有部署 PCIT EE**）。

```yaml
pipeline:

  install:
    image: khs1994/php:7.2.10-fpm-alpine
    commands:
      - composer install  

  script:
    image: khs1994/php:7.2.10-fpm-alpine
    commands:
      - vendor/bin/phpunit

  after_success:
    image: bash
    commands:
      - echo "build is success"    
```

* 推送到 GitHub，PCIT 开始进行 CI 测试、构建、部署

> 若想查看构建的聚合页面(详情，管理)，请登录 https://ci.khs1994.com/login

## PCIT CE vs PCIT EE

执行 CI 构建需要耗费 **计算资源**，本项目公有云因 **缺少资金** 暂不能提供构建功能。

为了表述方便将 PCIT 分为 CE 和 EE 两大版本。

## 尝试 Demo (PCIT CE)

PCIT 社区版(CE) 供用户体验 Demo。仅支持 [Issues 中英互译](https://github.com/khs1994-php/pcit/issues/95)。

[安装 GitHub App](https://github.com/khs1994-php/pcit/tree/master/docs)

> 可以通过 [捐赠](https://zan.khs1994.com) PCIT 项目，未来在社区版 (CE) 中可能会支持 CI 构建功能。

## 私有部署 (PCIT EE)

> 仅 PCIT EE 支持全部的 CI 功能(构建 测试 部署)。

* ~~PHP~~

* ~~MySQL~~

* ~~Redis~~

* ~~RabbitMQ~~

* **仅仅** 需要 [Docker](https://github.com/yeasy/docker_practice/tree/master/install) 和 [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp) 和 [Website SSL/TLS Certificates](https://github.com/Neilpang/acme.sh)

你可以通过以下方法私有部署 **PCIT EE** :

```bash
# 安装 khs1994-docker/lnmp

$ git clone https://github.com/khs1994-docker/lnmp.git ~/lnmp

# 中国镜像
# $ git clone https://gitee.com/khs1994-docker/lnmp.git ~/lnmp

$ composer create-project khs1994/pcit ~/lnmp/app/pcit

$ cd lnmp
```

**1.** 在 GitHub [Settings > Developer settings > OAuth Apps](https://github.com/settings/developers) 注册一个 GitHub Oauth App，用于 **OAuth2** 账号体系

**2.** 在 GitHub [Settings > Developer settings > GitHub Apps](https://github.com/settings/apps) 注册一个 GitHub App

**3.** 编辑 `~/lnmp/app/public/.env.development` 文件中的变量，之后启动 PCIT

```bash
$ ./lnmp-docker pcit-up
```

**4.** 点击刚才注册好的 GitHub App，选择仓库进行安装。

**5.** Git 仓库根目录包含 `.pcit.yml` 文件，推送到 GitHub，在 Commit 详情处查看构建。

更多信息请查看 https://github.com/khs1994-php/pcit/blob/master/docs/install/ee.md

### 视频教程

文字版看不明白？请查看 [视频版]() 安装教程。

## 开发团队支持

![](https://user-images.githubusercontent.com/16733187/41222863-c610772e-6d9a-11e8-8847-27ac16c8fb54.jpg)

关注 PCIT **微信公众平台** **[微博](https://weibo.com/kanghuaishuai)** **[QQ 群 894134241](https://shang.qq.com/wpa/qunwpa?idkey=776defd7c271e9de70b9dfae855a34f11aada1fec9f27d22303dfffcb6d75e63)** 寻求 **PCIT** 团队支持。

## PCIT 子项目

* [Docker PHP SDK](https://github.com/khs1994-docker/libdocker)

* [Docker Registry PHP SDK](https://github.com/khs1994-docker/libregistry)

* [Tencent AI CLI]()

* [WeChat PHP SDK](https://github.com/khs1994-php/libwechat)

* [DingTalk PHP SDK]()

* [GitHub CLI]()

* [Gitee CLI]()

* [Gogs CLI]()

## 项目拆分

未来本项目可能会将以下模块进行拆分

* [前端 UI](https://github.com/khs1994-php/pcit-ui)
* [YAML 转为 Docker 配置](https://github.com/khs1994-php/pcit-yaml)
* [缓存后端驱动](https://github.com/khs1994-php/pcit-cache)

## 致谢

* [PHP](https://www.php.net)

* [Docker](https://www.docker.com)

* [Kubernetes](https://kubernetes.io/)

* [Travis CI](https://travis-ci.com)

* [Drone CI](https://drone.io)

### GitHub 上的其他 CI/CD 项目

* https://github.com/topics/continuous-integration?l=php&o=desc&s=stars

* https://github.com/topics/continuous-integration?o=desc&s=stars

## 什么是云原生 Cloud Native?

Cloud native computing uses an open source software stack to be:

1. **Containerized.** Each part (applications, processes, etc) is packaged in its own container. This facilitates reproducibility, transparency, and resource isolation.
2. **Dynamically orchestrated.** Containers are actively scheduled and managed to optimize resource utilization.
3. **Microservices oriented.** Applications are segmented into microservices. This significantly increases the overall agility and maintainability of applications.
