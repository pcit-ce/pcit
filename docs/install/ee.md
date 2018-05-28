# KhsCI EE

## 运行环境准备

安装好 Docker 并克隆部署环境（LNMP）

```bash
$ git clone https://github.com/khs1994-docker/lnmp

$ cd lnmp

$ ./lnmp-docker.sh khsci-up
```

## 准备网站 TLS 证书

将 **公钥** **私钥** 文件内容合在一起放入 `khsci/ssl/ci.crt` 文件中。

## 配置 NGINX

编辑 `khsci/conf/khsci.conf`

## 新建 GitHub App

在 GitHub [Settings > Developer settings > GitHub Apps](https://github.com/settings/apps) 新建 GitHub App

部分重要信息填写如下：

* `Homepage URL` 网站域名 https://ci.example.com:port

* `User authorization callback URL` OAuth 回调地址 https://ci.example.com:port/oauth/github_app

* `Webhook URL` Webhooks 接收地址 https://ci.example.com:port/webhooks/github_app

创建完毕之后，生成应用私钥

在 GitHub App 设置(General)->Private keys->右边按钮(Generate a private key)，下载到本地

假设该私钥文件名为 `khsci.2018-04-28.private-key.pem`

放入到 `lnmp/app/khsci/public/private_key` 目录中

## 填写关键信息

编辑 `lnmp/app/khsci/public/.env.production` 文件，设置好相关变量

特别注意以下变量

```bash
CI_GITHUB_APP_PRIVATE_FILE=
```

此变量值为上一步生成的私钥文件名，例如 `khsci.2018-04-28.private-key.pem`

其他变量含义请查看 [ENV](env.md)

## 启动

```bash
$ lnmp-docker.sh khsci-up
```

## 准备项目

项目根目录包含 `.khsci.yml`，推送到 GitHub，在 commit 信息处点击小图标查看详情。
