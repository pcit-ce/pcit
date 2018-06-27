# pipeline

## 基本指令

`pipeline` 指令用来设置具体的构建步骤，每个步骤均运行在容器中，所以每个步骤必须首先指定 Docker Image 名称 (`image`) 和具体的构建指令 (`commands`) 。

> pipeline 指令可以包含多个构建步骤

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.2.5-alpine3.7
    commands:
      - composer install -q
      - vendor/bin/phpunit

  public:
    ...

  deploy:
    ...      
```

## 环境变量

除了以上两个基本指令外，还可以像我们平时使用 `$ docker container run` 命令那样设置 **环境变量** 等信息。

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.2.5-alpine3.7
    environment:
      - key=value
    commands:
      - composer install -q
      - vendor/bin/phpunit
```

## 镜像拉取策略

每次构建时，无论 Docker Image 是否存在总是拉取镜像，可以使用 `pull` 指令。

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.2.5-alpine3.7
    pull: true
    commands:
      - composer install -q
      - vendor/bin/phpunit
```

## shell

default shell is `sh`

```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.2.5-alpine3.7
    shell: bash
```

## 构建条件

还可以设置构建条件，通过 `when` 指令设置。


```yaml
pipeline:
  php:
    image: khs1994/php-fpm:7.2.5-alpine3.7
    commands:
      - composer install -q
      - vendor/bin/phpunit
    when:
      event: tag
```

以上设置构建仅在 Git 打标签之后执行，其余例如 push、pull_request 均不构建。

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
```
