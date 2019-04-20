# PCIT 插件

```php
$adapter = new PAGES([]);

$plugin = new Application($adapter,$config);

[$image,$env] = $plugin->deploy();

// 将 $env 传入 Docker 实例。
```

## 项目状态

该组件仍内置于 `pcit-ce/pcit` 中，未来将分离为独立组件。

## 自定义插件

```php
<?php

namespace PCIT\Plugin\Adapter;

class S3 extends AbstractAdapter {
  public function __construct(array $config){}
  public function deploy(): array {}  
}
```
