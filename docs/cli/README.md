# PCIT CLI

PCIT CLI 是命令行工具，具有校验 `.pcit.yml` 文件，获取构建状态等功能。

## 申请令牌

**GitHub** https://github.com/settings/tokens/new

**Gitee** https://gitee.com/profile/personal_access_tokens

**Coding**

## 使用

**通过 Docker 使用**

```bash
$ export CI_HOST="https://ci.domain.com"

$ docker run -it --rm -e CI_HOST -v ${PWD}:/workspace pcit/cli CMD

$ docker run -it --rm -e CI_HOST -v ${PWD}:/workspace pcit/cli validate
```

**通过 Composer 安装(暂不可用)**

```bash
$ composer global require pcit/pcit

$ pcit CMD
```

**下载使用**

```bash
$ curl -LO https://github.com/pcit-ce/pcit-release/releases/download/nightly/pcit.phar

$ chmod +x pcit.phar

$ export CI_HOST="https://ci.domain.com"
$ ./pcit.phar CMD

# windows

$ $env:CI_HOST="https://ci.domain.com"
$ php ./pcit.phar CMD
```

## 全局参数

`-e, --api-endpoint` 指定 API 入口网址，取决于您私有部署 PCIT 的网址 (默认值通过环境变量 `CI_HOST=https://ci.domain.com` 读取)

`-g, --git_type` Git 服务商，`github` `gitee` `coding`

`-r, --repo` 指定 Git 仓库名称， `pcit-ce/pcit`
