# jobs

`jobs` 用来设置构建矩阵。常见的场景就是项目需要在不同的软件版本中进行测试。

例如我们需要在 PHP 7.4 和 7.3 版本中进行测试。

```yaml
steps:
  php:
    image: khs1994/php-fpm:${PHP_TAG}
    ...

jobs:
  PHP_TAG:
    - 7.4.6-alpine
    - 7.3.18-alpine    
```

我们在 `image` 指令中设置变量 `${PHP_TAG}`

在 `jobs` 中设置变量值

以上构建相当于

```yaml
steps:
  php:
    image: khs1994/php-fpm:7.4.6-alpine
    ...

  php2:
    image: khs1994/php-fpn:7.3.18-alpine
    ...
```

很明显 `jobs` 指令的使用可以简化 `steps` 指令。同时很适用于项目需要在多个软件版本中进行测试、构建的场景。
