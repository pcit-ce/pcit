# KhsCI CE

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/khsci.svg?style=social&label=Stars)](https://github.com/khs1994-php/khsci) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/khsci.svg)](https://packagist.org/packages/khs1994/khsci) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/khsci/all.svg)](https://github.com/khs1994-php/khsci/releases) [![Build Status](https://ci.khs1994.com/github_app/khs1994-php/khsci/status?branch=master)](https://ci.khs1994.com/github_app/khs1994-php/khsci)

**国内首个基于 GitHub Checks API 使用 PHP 编写的运行于 Docker 之上的由 Tencent AI 驱动的 CI/CD 系统**

* [支持文档](https://github.com/khs1994-php/khsci/tree/master/docs)

* [Changelog](https://github.com/khs1994-php/khsci/blob/master/CHANGELOG.md)

* [问题反馈](https://github.com/khs1994-php/khsci/issues)

* [API](https://ci.khs1994.com/api)

* [捐赠](https://zan.khs1994.com)

* [KhsCI EE](https://github.com/khs1994-php/khsci/tree/master/enterprises)

## PHP CaaS

**Powered By [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp)**

## About KhsCI

**KhsCI** 由 **PHP 后端**（Webhooks Server + Daemon CLI） + **GitHub App** 组成。

用户安装 GitHub App，即可使用，无需 **额外** 注册、登录、跳转。唯一需要做的就是仓库根目录包含 [`.khsci.yml`](https://github.com/khs1994-php/khsci/tree/master/yml_examples) 文件。

* [什么是 GitHub App](https://github.com/khs1994-php/khsci/issues/51)

所以想体验 KhsCI 有两种方案:

一是直接安装 [GitHub App KhsCI](https://github.com/khs1994-php/khsci/tree/master/docs)，体验 Demo（暂不提供 Docker 构建，仅提供 **Issue** **Pull Requests** 相关功能）。

二是自己部署 PHP 后端，自己新建 GitHub 应用，安装自己的 GitHub 应用（支持 Docker 构建）。

## Try Demo

[Install GitHub App](https://github.com/khs1994-php/khsci/tree/master/docs)

## Installation In Your host

* ~~MySQL~~

* ~~Redis~~

* ~~RabbitMQ~~

* **ONLY** need [Docker](https://github.com/yeasy/docker_practice/tree/master/install) and [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp)

To **install** KhsCI, simply:

```bash
# install khs1994-docker/lnmp

$ git clone --recursive https://github.com/khs1994-docker/lnmp.git ~/lnmp

$ composer create-project khs1994/khsci ~/lnmp/app/khsci @dev

$ cd lnmp

$ ./lnmp-docker.sh khsci-up
```

## 子项目

* [Docker PHP](https://github.com/khs1994-docker/libdocker)

* [Docker Registry PHP](https://github.com/khs1994-docker/libregistry)

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

