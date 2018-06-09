# KhsCI

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/khsci.svg?style=social&label=Stars)](https://github.com/khs1994-php/khsci) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/khsci.svg)](https://packagist.org/packages/khs1994/khsci) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/khsci/all.svg)](https://github.com/khs1994-php/khsci/releases) [![Build Status](https://ci2.khs1994.com:10000/github/khs1994-php/khsci/status?branch=master)](https://ci.khs1994.com/github/khs1994-php/khsci) [![codecov](https://codecov.io/gh/khs1994-php/khsci/branch/master/graph/badge.svg)](https://codecov.io/gh/khs1994-php/khsci)

**国内首个基于 GitHub Checks API 使用 PHP 编写的运行于 Docker 之上的由 Tencent AI 驱动的 CI/CD 系统**

* [支持文档](https://docs.ci.khs1994.com)

* [Changelog](https://github.com/khs1994-php/khsci/blob/master/CHANGELOG.md)

* [问题反馈](https://github.com/khs1994-php/khsci/issues)

* [API](https://ci.khs1994.com/api)

* [API 文档](https://api.ci.khs1994.com)

* [捐赠](https://zan.khs1994.com)

* [KhsCI EE](https://github.com/khs1994-php/khsci/tree/master/docs#about-khsci-ce-and-ee)

## 诚邀各位 PHP 开发者加入本项目

欢迎各位通过 Pull_Request 参入贡献本项目。

## PHP CaaS

**Powered By [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp)**

## About KhsCI

**KhsCI** 由 **PHP 后端**（`Webhooks Server` + `Daemon CLI`） + **GitHub App** + **CLI** 三部分组成

* **Webhooks Server** 接收 GitHub App POST 过来的数据

* **Daemon CLI** 后端常驻 (守护) 程序，处理 GitHub App POST 过来的数据并返回给 `GitHub App`

* **CLI** 提供各种实用的功能，例如 命令行操作 GitHub，命令行中英互译，等

* [什么是 GitHub App](https://github.com/khs1994-php/khsci/issues/51)

用户安装 `GitHub App`，即可使用，无需 **额外** 注册、登录、跳转。唯一需要做的就是仓库根目录包含 [`.khsci.yml`](https://github.com/khs1994-php/khsci/tree/master/yml_examples) 文件。

所以想体验 **KhsCI** 有 **两种** 方案:

**一是** 直接安装 [GitHub App KhsCI](https://github.com/khs1994-php/khsci/tree/master/docs)，体验 Demo（暂不提供 `Docker` 构建，仅提供 **Issue**、**Pull Requests** 相关功能）。

**二是** 自己部署 `PHP` 后端，自己新建 `GitHub App`，安装自己的 `GitHub App`（支持 `Docker` 构建）。

## Try Demo (KhsCI CE)

[Install GitHub App](https://github.com/khs1994-php/khsci/tree/master/docs)

## Self-Hosting (KhsCI EE)

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

## Thanks

* [Drone CI](https://drone.io)

* [Travis CI](https://travis-ci.com)

## 子项目

* [Docker PHP](https://github.com/khs1994-docker/libdocker)

* [Docker Registry PHP](https://github.com/khs1994-docker/libregistry)

* [Tencent AI CLI]()

* [Kubernetes PHP]()

* [Wechat PHP](https://github.com/khs1994-php/libwechat)

* [QQ PHP]()

* [DingTalk PHP]()

* [Baidu xiongzhang PHP](https://github.com/khs1994-php/xiongzhang)

* [GitHub CLI]()

* [Gitee CLI]()

* [Coding CLI]()

* [Aliyun CLI]()

* [Tgit CLI]()

* [GitLab CLI]()

* [Gogs CLI]()

## CI/CD

* [Drone](https://www.khs1994.com/categories/CI/Drone/)

* [Travis CI](https://travis-ci.org/khs1994-php/khsci)

* [Style CI](https://styleci.io/repos/119219872)

* [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

* [Renovate](https://github.com/marketplace/renovate)

* [Dependabot](https://github.com/marketplace/dependabot)

* [Aliyun CodePipeline](https://www.aliyun.com/product/codepipeline)

* [Tencent Cloud Continuous Integration](https://cloud.tencent.com/product/cci)

* [Docker Build Powered By Tencent Cloud Container Service](https://cloud.tencent.com/product/ccs)

* [Docker Build Powered By Docker Cloud](https://cloud.docker.com)

* [Docker Build Powered By Aliyun Container Service](https://www.aliyun.com/product/containerservice)

### Other CI/CD on GitHub

* https://github.com/topics/continuous-integration?l=php&o=desc&s=stars

* https://github.com/topics/continuous-integration?o=desc&s=stars
