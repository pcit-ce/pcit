# CHANGELOG

## v18.06.0

### 2018/06/07

* [ ] 需要新建两个应用 OAuth App 用于网站登录和获取用户基本信息，GitHub App 用于构建。

### 2018/06/06

* [ ] Git Clone 支持设置 Hosts

### 2018/06/06

* [ ] 支持仓库镜像，一键同步仓库到多个 Git 服务商

### 2018/06/06

* [ ] Pull_request 增加 **php-cs-fixer** label，KhsCI 可以自动修复代码风格

### 2018/06/05

* [*] Pull_request 增加 **merge** label，KhsCI 测试通过之后可以自动 merge

### 2018/06/03

* [ ] AI + CI，人脸登录网站? 代号 `KhsCI Hello`

### 2018/06/03

* [ ] 通知消息免打扰，特定时间段内不发送通知消息

### 2018/05/30

* [x] 支持设置 `CI_ROOT`, 仅构建 `CI_ROOT` 名下的仓库。

### 2018/05/30

* [ ] 支持微信扫码绑定账号

### 2018/05/30

* [x] 支持自定义阿里云 Docker 镜像构建的 Webhooks 地址 `CI_ALIYUN_REGISTRY_WEBHOOKS_ADDRESS`，原因缺乏认证手段。

### 2018/05/29

* [ ] 设计分布式构建方案。Swarm 和 Kubernetes

### 2018/05/29

* [ ] 内置 File Server 存储构建缓存，Travis CI 的方案是存放到对象存储

### 2018/05/29

* [ ] 开发小程序

### 2018/05/29

* [x] 原生支持微信公众平台，填写 **相关密钥** 即可一键开启模板消息推送（微信公众平台测试号 100 人限制，正式号有各种限制）。

### 2018/05/29

* [ ] 支持邮件通知仓库管理员。

### 2018/05/28

* [x] 插件通过 Docker 镜像实现。通过设置必要的环境变量实现功能。

### 2018/05/28

* [ ] 文件发布到 Git Release

* [ ] 文件发布到 GitHub Pages

* [ ] 文件发布到 对象存储

### 2018/05/28

* [x] KhsCI 虽然同时支持多个 Git 服务商，但各个 Git 服务商之间保持绝对独立，不支持跨 Git 服务商管理，即 GitHub 的账号不能管理 Gitee。

* [x] 不支持账号整合，关联。故，api 的 url 涉及到需要认证的，无需包含 git_type。

### 2018/05/28

* [ ] 处于构建过程中的 builds, 实现日志 **实时** 输出

### 2018/05/27

* [*] GitHub App 提供的 OAuth 获取不到用户名下的组织列表，所以网站登录使用 OAuth App。

### 2018/05/26

* [x] 构建使用的 Docker 镜像暂时只支持公有镜像。

### 2018/05/26

* [x] 设计分布式部署方案

### 2018/05/25

* [x] 支持 GitHub App Check Run [Action](https://developer.github.com/changes/2018-05-23-request-actions-on-checks/)

### 2018/05/22

* [ ] 完成 API Access Token、CLI

### 2018/05/19

* [ ] 设计 API

### 2018/05/18

* [ ] 增加 Tencent AI CLI `bin/tencent.ps1` `bin/tencent.sh`

### 2018/05/18

* [x] 支持 **阿里云** Docker 镜像构建服务 Webhooks

### 2018/05/17

* [x] 增加内置系统环境变量

### 2018/05/16

* [x] 区分内外 PR，即 PR 是从内部仓库发起，还是从他人仓库发起，这影响着后续 Secret 的功能。

### 2018/05/15

* [x] 使用 `.khsci.yml` 定义一切 https://github.com/khs1994-php/khsci/issues/66

### 2018/05/14

* [x] `Tencent AI + Issue = ?`，欢迎体验 https://github.com/khs1994-php/khsci/issues/64

### 2018/05/14

* [x] 提交 PR 实现自动回复

* [ ] 指定代码审阅者 (`reviewers`), 打标签（`label`）, 指定给某人 (`assign`), 关联项目 (`project`), 关联里程碑 (`milestone`)

### 2018/05/13

* [x] 数据库 **软删除** 不直接删除数据, 而是通过检查标记 `deleted_at` 来确定数据是否有效（TODO）

### 2018/05/13

* [x] 提出 Issue 实现自动回复(中英互译)

* [ ] AI 提取关键词，自动打标签 (`label`)

* [ ] 无用问题自动 **关闭** 加 **锁定**

### 2018/05/13

* [x] 引入 [Tencent AI](https://github.com/khs1994-php/tencent-ai)，[讨论](https://github.com/khs1994-php/khsci/issues/61).

### 2018/05/13

* [x] KhsCI 是国内首家支持 GitHub [Checks API](https://blog.github.com/2018-05-07-introducing-checks-api/) 的 CI/CD 系统

### 2018/05/09

* [x] **2018** Only Support GitHub Apps

### 2018/05/08

* [ ] **后台任务** 刷新用户仓库列表

### 2018/05/07

* [x] **后台任务** 刷新处于活跃状态的仓库的管理员和协作者信息

### 2018/05/06

* [x] **后台任务** 暂时一次只构建一个任务

### 2018/05/03

* [x] 强制将 `tmp` 数据卷挂载到 `/tmp` 目录

### 2018/04/29

* [x] GitHub Commit 能够展示构建状态

### 2018/04/19

* [x] **强制** 使用 HTTPS

### 2018/04/18

* [x] 完成 `Webhooks` 获取、增加、删除

### 2018/04/15

* [x] 后端统一返回 `JSON` 格式

### 2018/04/14

* [x] 所有配置通过 `.env` 载入

### 2018/04/13

* [x] 完成 OAuth 登录、基本信息获取、Git Repo 列表获取
