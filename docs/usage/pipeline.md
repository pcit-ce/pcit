# pipeline

## 基本指令

`pipeline` 指令用来设置具体的构建步骤，每个步骤均运行在容器中，所以每个步骤必须首先指定 Docker Image 名称 (`image`) 和具体的构建指令 (`commands`) 。

> pipeline 指令可以包含多个构建步骤

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.4.2-alpine
    commands:
      - composer install -q
      - vendor/bin/phpunit

  public:
    ...

  deploy:
    ...      
```

## 1. `image`

设置构建步骤所使用的镜像。

## 2. `commands`

设置构建步骤所运行的命令。

## 3. `environment` 环境变量

除了以上两个基本指令外，还可以像我们平时使用 `$ docker container run -e "K=V" ...` 命令那样设置 **环境变量**。

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.4.2-alpine
    environment:
      - key=value
    commands:
      - composer install -q
      - vendor/bin/phpunit
```

## 4. `pull` 镜像拉取策略

每次构建时，无论 Docker Image 是否存在总是拉取镜像，可以使用 `pull: true` 指令(默认为 `false`)。

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.4.2-alpine
    pull: true
    commands:
      - composer install -q
      - vendor/bin/phpunit
```

## 5. `shell`

默认的 shell 为 `sh`，你可以改为 `bash`

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.4.2-alpine
    shell: bash
```

全部支持的 `shell` 包括 `sh` `bash` `python` `pwsh` `node`

## 6. `when` 构建条件

可以通过 `when` 指令设置构建条件。

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.4.2-alpine
    commands:
      - composer install -q
      - vendor/bin/phpunit
    when:
      event: tag
```

增加以上的 `when` 指令之后，构建仅在 Git 打标签之后执行，当 push、pull_request 均不进行构建。

全部可用的设置如下：

```yaml
when:
  # platform: linux/amd64
  # platform:  [ linux/*, windows/amd64 ]

  # status: changed
  # status:  [ failure, success ]

  # event: tag
  # event: [push, pull_request, tag, deployment]
  event: [push, pull_request, tag]

  # branch: master
  # branch: prefix/*
  # branch: [master, develop]
  # branch:
  #   include: [ master, release/* ]
  #   exclude: [ release/1.0.0, release/1.1.* ]

  # matrix:
  # - K: v
  #   K2: v2
  #   K3: v3
```

## 7. `privileged`

与 `docker run --privileged` 参数的行为一致。

## 8. `settings`

该指令用来配置插件。

```yaml
pipeline:
  settings:
    provider: docker
    k: v
    k2: v2
```

将 `PCIT_K=v PCIT_K2=v2` 作为环境变量传入容器中。
