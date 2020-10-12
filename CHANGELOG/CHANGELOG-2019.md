# CHANGELOG

* [18.12.0](https://github.com/pcit-ce/pcit/compare/18.12.0-beta1...18.12.0)
* [18.12.0-beta1](https://github.com/pcit-ce/pcit/compare/18.12.0-alpha7...18.12.0-beta1)
* [18.12.0-alpha7](https://github.com/pcit-ce/pcit/compare/18.12.0-alpha6...18.12.0-alpha7)
* [18.12.0-alpha6](https://github.com/pcit-ce/pcit/compare/18.12.0-alpha5...18.12.0-alpha6)
* [18.12.0-alpha5](https://github.com/pcit-ce/pcit/compare/18.12.0-alpha4...18.12.0-alpha5)
* [18.12.0-alpha4](https://github.com/pcit-ce/pcit/compare/18.12.0-alpha3...18.12.0-alpha4)
* [18.12.0-alpha3](https://github.com/pcit-ce/pcit/compare/18.12-alpha2...18.12.0-alpha3)
* [18.12.0-alpha2](https://github.com/pcit-ce/pcit/compare/18.12-alpha1...18.12-alpha2)
* [18.12.0-alpha1](https://github.com/pcit-ce/pcit/compare/18.06.0...18.12-alpha1)

## v18.12

### 2020/01/19

* [x] Builds 列表不包含 `misconfigured` 类型的 build
* [x] .pcit.yml `matrix` is an alias for `jobs`
* [x] 一个 git 事件，触发一个 `build`，一个 build 包含 `jobs`, 一个 job 包含 `steps`

### 2020/01/01

* [x] 支持使用 actions

### 2019/11/14

* [ ] 实现 Webhooks Client

### 2019/09/24

* [x] `shell` 指令支持 `sh(默认)` `bash` `pwsh` `python`

### 2019/01/20

* [x] 新增 `image` 指令，用来配置构建步骤的默认镜像

### 2019/01/15

* [x] **安全** Pull_request 事件不上传缓存

### 2019/01/14

* [ ] 新增 `cron` 事件，支持设置构建步骤仅在 `cron` 事件中执行，适用场景: 提供软件的 `每日构建(nightly)` 版

### 2019/01/08

* [x] PCIT 官方 Docker 镜像使用 `pcit/*`

### 2019/01/07

* [x] 支持全局 `hosts`，通过新的 `networks` 指令进行设置
* [x] 支持简化的 `services` 指令
* [x] 使用 `BuildKit` 构建 Docker 镜像
