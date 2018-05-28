# CHANGELOG

## v18.06.0

### 2018/05/28

插件通过 Docker 镜像实现。

通过设置必要的环境变量实现功能。

### 2018/05/28

支持 TAG 构建成功后执行以下动作

文件发布到 Git Release

文件发布到 对象存储

文件发布到 GitHub Pages

### 2018/05/28

KhsCI 虽然同时支持多个 Git 服务商，但各个 Git 服务商之间保持绝对独立，不支持跨 Git 服务商管理，即 GitHub 的账号不能管理 Gitee。

不支持账号整合，关联。

故，api 的 url 涉及到需要认证的，无需包含 git_type。

### 2018/05/28

处于构建过程中的 builds, 实现日志 **实时** 输出

### 2018/05/27

GitHub App 提供的 Oauth 获取不到用户名下的组织列表，仍通过 OAuth 认证，还是跳过用户仓库展示。

### 2018/05/26

构建使用的 Docker 镜像暂时只支持公有镜像。

### 2018/05/26

设计分布式部署方案

### 2018/05/25

支持 GitHub App Check Run [Action](https://developer.github.com/changes/2018-05-23-request-actions-on-checks/)

### 2018/05/22

完成 API Access Token、CLI

### 2018/05/19

设计 API

### 2018/05/18

增加 Tencent AI CLI `bin/tencent.ps1` `bin/tencent.sh`

### 2018/05/18

支持 **阿里云** Docker 镜像构建服务 Webhooks

`public/config/aliyun_docker_registry.json`

`{CI_HOST}/webhooks/aliyun_docker_registry`

### 2018/05/17

增加内置系统环境变量

### 2018/05/16

区分内外 PR，即 PR 是从内部仓库发起，还是从他人仓库发起，这影响着后续 Secret 的功能。

### 2018/05/15

使用 `.khsci.yml` 定义一切 https://github.com/khs1994-php/khsci/issues/66

### 2018/05/14

`Tencent AI + Issue = ?`，欢迎体验 https://github.com/khs1994-php/khsci/issues/64

### 2018/05/14

提交 PR 实现自动回复

指定代码审阅者 (`reviewers`), 打标签（`label`）, 指定给某人 (`assign`), 关联项目 (`project`), 关联里程碑 (`milestone`)

自动 Merge

### 2018/05/13

数据库 **软删除** 不直接删除数据, 而是通过检查标记 `deleted_at` 来确定数据是否有效（TODO）

### 2018/05/13

提出 Issue 实现自动回复

打标签 (`label`), 指定给某人 (`assign`), 关联项目 (`project`), 关联里程碑 (`milestone`)

无用问题自动 **关闭** 加 **锁定**

超时问题（最后回复时间）自动关闭

### 2018/05/13

引入 [Tencent AI](https://github.com/khs1994-php/tencent-ai)，[讨论](https://github.com/khs1994-php/khsci/issues/61).

### 2018/05/13

KhsCI 是国内首家支持 GitHub [Checks API](https://blog.github.com/2018-05-07-introducing-checks-api/) 的 CI/CD 系统

### 2018/05/09

**2018** Only Support GitHub Apps

### 2018/05/08

**后台任务** 刷新用户仓库列表

### 2018/05/07

**后台任务** 刷新处于活跃状态的仓库的管理员和协作者信息

### 2018/05/06

**后台任务** 暂时一次只构建一个任务

### 2018/05/03

强制将 `tmp` 数据卷挂载到 `/tmp` 目录

### 2018/04/29

GitHub Commit 能够展示构建状态

### 2018/04/19

**强制** 使用 HTTPS

### 2018/04/18

完成 `Webhooks` 获取、增加、删除

### 2018/04/15

后端统一返回 `JSON` 格式

### 2018/04/14

所有配置通过 `.env` 载入

### 2018/04/13

完成 OAuth 登录、基本信息获取、Git Repo 列表获取
