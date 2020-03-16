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
