# PCIT 部署组件

```php
$adapter = new PAGES([]);

$deployer = new Application($adapter,$config);

[$image,$env] = $deployer->deploy();

// 将 $env 传入 Docker 实例。
```

## 项目状态

该组件仍内置于 `pcit-ce/pcit` 中，未来将分离为独立组件。

## 自定义部署组件

```php
<?php

namespace PCIT\Deployer\Adapter;

class S3 extends AbstractAdapter {
  public function __construct(array $config){}
  public function deploy(): array {}  
}
```
