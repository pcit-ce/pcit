# PCIT EE

## 运行环境准备

安装好 Docker 并克隆 LNMP 部署环境

```bash
$ git clone --depth=1 https://github.com/khs1994-docker/lnmp ~/lnmp

$ cd ~/lnmp
```

## 准备网站 TLS 证书

将 **公钥** **私钥** 文件内容合在一起放入 `~/lnmp/pcit/ssl/ci.crt` 文件中。

## 配置 NGINX

编辑 `~/lnmp/pcit/conf/pcit.conf`

## 新建 GitHub OAuth App

在 GitHub [Settings > Developer settings > OAuth Apps](https://github.com/settings/developers) 注册一个 GitHub Oauth App

部分重要信息填写如下：

* `Authorization callback URL` OAuth 回调地址 https://ci.example.com:port/oauth/github

## 新建 GitHub App

在 GitHub [Settings > Developer settings > GitHub Apps](https://github.com/settings/apps/new) 新建 GitHub App

部分重要信息填写如下：

* `Homepage URL` 网站域名 `https://ci.example.com:port`

* `Webhook URL` Webhooks 接收地址 `https://ci.example.com:port/webhooks/github`

创建完毕之后，生成应用私钥

在 GitHub App 设置(**General**)-> `Private keys` ->右边按钮(**Generate a private key**)，下载到本地

例如该私钥文件名为 `pcit.2018-04-28.private-key.pem`

我们需要将其改名为 `private.key`，之后放入到 `~/lnmp/pcit/key` 目录中

## 填写关键信息

编辑 `~/lnmp/pcit/.env.development` 文件，设置好相关变量，变量含义请查看 [ENV](env.md)

## 启动

```bash
$ lnmp-docker pcit-up
```

## 准备项目

项目根目录包含 `.pcit.yml`，推送到 GitHub，在 commit 信息处点击小图标查看详情。
