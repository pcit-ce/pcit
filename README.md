# KhsCI CE

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/khsci.svg?style=social&label=Stars)](https://github.com/khs1994-php/khsci) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/khsci.svg)](https://packagist.org/packages/khs1994/khsci) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/khsci/all.svg)](https://github.com/khs1994-php/khsci/releases) [![KhsCI](https://ci2.khs1994.com:10000/github/khs1994-php/khsci/status)](https://ci2.khs1994.com:10000/github/khs1994-php/khsci)

国内首个基于 GitHub Checks API 使用 PHP 编写的运行于 Docker 之上的由 Tencent AI 驱动的 CI/CD 系统

* [支持文档](https://github.com/khs1994-php/khsci/tree/master/docs)

* [Changelog](https://github.com/khs1994-php/khsci/blob/master/CHANGELOG.md)

* [问题反馈](https://github.com/khs1994-php/khsci/issues)

* [API](https://ci.khs1994.com/api)

* [捐赠](https://zan.khs1994.com)

* [KhsCI EE](https://github.com/khs1994-php/khsci/tree/master/enterprises)

## PHP CaaS

**Powered By [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp)**

## Installation

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

