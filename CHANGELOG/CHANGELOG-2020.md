# CHANGELOG

* [19.12.0-alpha1](https://github.com/pcit-ce/pcit/compare/18.12.0...master)

## v19.12

### 2020/09/21

* [ ] 新增 `timeout` 指令

### 2020/09/04

* [x] 版本化 API `Accept: application/vnd.pcit.v1alpha1+json`

### 2020/08/21

* [x] `Agent` 通过 [JSON-RPC](http://wiki.geekdream.com/Specification/json-rpc_2.0.html) 与 `Server` 通信

### 2020/08/04

* [x] 不再需要 GitHub OAuth App，使用 GitHub App 的 OAuth 功能

### 2020/05/18

* [ ] Coding.net 不支持通过 OAuth 获取的 Token 克隆仓库。

### 2020/05/13

* [ ] 基于 [tekton](https://github.com/tektoncd/pipeline) 在 Kubernetes 上运行

### 2020/04/01

* [ ] secret 加密存储

### 2020/03/27

* [x] step 支持 `read_only: bool` 选项

### 2020/03/24

* [x] 支持禁用 git clone

### 2020/03/23

* [x] 支持上传资源(artifacts)，新增资源(artifacts)相关 API
* [x] git clone 支持重试(默认重试 1 次) `CI_GIT_CLONE_STEP_RETRY`
* [x] 新增 toolkit，通过数据卷挂载到 `/opt/pcit/toolkit/pcit-CMD`

### 2020/03/20

* [x] Build 日志隐藏 secret

### 2020/03/17

* [ ] `.pcit.yml` 必须经过验证，才能开始后续步骤

### 2020/03/11

* [x] 支持验证 `.pcit.yml` 文件

### 2020/02/19

* [ ] 拉取镜像超时
* [ ] 由于 `timeout` 命令限制，故只支持基于 `alpine:3.10` 及以上版本的镜像(仅指基于 alpine 的镜像，基于其他系统的镜像不受限制)
