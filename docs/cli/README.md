# PCIT CLI

PCIT CLI 是命令行工具，具有校验 `.pcit.yml` 文件，获取构建状态等功能。

## 通过 Docker 使用

```bash
$ docker run -it --rm -v ${PWD}:/workspace pcit/cli CMD

$ docker run -it --rm -v ${PWD}:/workspace pcit/cli validate
```

## 通过 Composer 安装(暂不可用)

```bash
$ composer global require pcit/pcit

$ pcit CMD
```

## 下载使用

```bash
$ curl -LO https://github.com/pcit-ce/pcit-release/releases/download/nightly/pcit.phar

$ chmod +x pcit.phar

$ ./pcit.phar CMD

# windows

php ./pcit.phar CMD
```

## 全局参数

`-e, --api-endpoint` 指定 API 入口网址，取决于您私有部署 PCIT 的网址

`-g, --git_type` Git 服务商类型，`github` `coding` `gitee` and more

`-r, --repo` 指定 Git 仓库名称， `pcit-ce/pcit`
