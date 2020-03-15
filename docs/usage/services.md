# services

有时我们的构建需要额外的服务，例如数据库、缓存等服务。我们可以通过 `services` 指令进行设置。

> 服务指令可以同时包含多个服务。

```yaml
services:
  mysql:
  redis:
  mongodb:    
```

你也可以对 **服务** 进行详细的设置，例如指定镜像名、命令等。

```yaml
services:
  mysql:
    image: mysql:${MYSQL_VERSION}
    env:
      - MYSQL_DATABASE=test
      - MYSQL_ROOT_PASSWORD=test
    # entrypoint: [ "mysqld" ]
    # command: [ "--character-set-server=utf8mb4", "--default-authentication-plugin=mysql_native_password" ]

  redis:
    ...
```

我们的构建项目如何连接服务呢？下面以 PHP 的 PDO 驱动连接 `MySQL` 为例。

```php
$pdo = new PDO('mysql:host=mysql;dbname=test;port=3306','root','test');
```

这里 host 对应着每个服务的名字。

## 默认配置

### Redis

版本 `5.0.3`
密码 `无`

### MySQL

版本 `5.7.24`
密码 `test`
