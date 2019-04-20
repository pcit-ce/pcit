---
home: true
actionText: PCIT is CI TOOLKIT Written by PHP
actionLink: https://ci.khs1994.com
features:
- title: 安全
  details: 除构建日志、项目特征信息外，本系统不存储任何用户数据、仓库代码
- title: 便捷
  details: 基于 OAuth2 协议，PCIT 不拥有账号体系，直接基于 Git 服务商账号体系
- title: 协作
  details: 基于 GitHub 等的多人协作
footer: Copyright @2019 khs1994.com
---

# PCIT Documents

**PCIT** 划分为 **CE** 和 **EE** 两大版本。

**CE** 供用户预览，**不提供** 项目构建功能。

**EE** 需要用户在 **自有主机** 私有部署，**支持** 项目构建。

## 一、体验 PCIT CE

### 1. GitHub 新建仓库

### 2. 安装 PCIT GitHub App

在 https://github.com/apps/pcit-ce 点击右边的 `Install` 按钮，在稍后跳转的页面中选择你新建的 **仓库**

### 3. 体验 Issue 标题中文翻译为英文

在你的 **仓库** 新建一个 Issue (标题为中文)，提交之后，可以看到 `pcit[bot]` 将这个标题修改为 了英文。

## 二、PCIT EE

**PCIT EE 需要自行部署，部署方法请看下一节** 本小节介绍如何使用 PCIT 进行项目测试、构建。

### 1. 准备 PHP 项目

### 2. 初始化 `.pcit.yml` 文件

```bash
$ pcitinit php
```

### 3. 推送到 GitHub

```bash
$ cd myProject

# 编辑文件，然后推送到 GitHub

$ git add .

$ git commit -m "Test PCIT"

$ git push origin master
```

在 **GitHub** 点击 `commits`，点击 commit 信息后的小图标，进入到构建详情页，查看构建过程及结果

## 三、私有部署 PCIT EE

**PCIT** 由 **PHP 后端**（Webhooks Server + Daemon CLI）和 **GitHub App** 组成。

[立即部署](https://github.com/pcit-ce/pcit/blob/master/docs/install/ee.md)

## 四、PCIT CE vs EE

| Compare       | PCIT CE             | PCIT EE      |
| :------------ | :------------------- | :------------ |
| Pricing       | Free                 | **$0**/month  |
| 支持方式       | 社区支持                    | 项目开发者一对一支持    |
| 使用方式       | 一键安装 `GitHub App` 体验  | **自有主机** 私有部署 |
| 仓库数量       | 公共仓库不限制               | 公共仓库不限制       |
| 协作者         | 不限制协作者                | 不限制协作者        |
| 通知方式       | Git Issues                | Git Issues    |
| 项目构建       | 不支持                    | 支持            |
| Issue Comment | 支持                     | 支持            |

* 协作者数量取决于 Git 服务商的限制，PCIT 不做任何限制

* 自有主机包括 **树莓派**、**PC**、**笔记本**、**云主机**

* 本项目优先支持 GitHub，部分功能可能不支持其他 Git 服务商

## 五、未来 CE 和 EE 版本价格问题

总的来说 **CE** 和 **EE** 的最大区别就是 **是否需要自己部署**。

> 为什么 CE 不提供构建功能?

由于 **CE** 部署在一个 **1G 1核** 的云主机上，配置较低，暂不提供项目构建功能。

本项目承诺 **永久免费**，但对 **技术支持服务** 收费（ **如何收费待定** ）。

## 支持 PCIT CE 项目

访问 https://ci.khs1994.com/donate 赞助 **PCIT** 项目。
