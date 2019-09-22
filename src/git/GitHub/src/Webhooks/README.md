# WebHooks

PCITDaemon 处理 Webhooks 数据，存储到数据库。

```php
$webhooksHandler = new Kernel();

// save data to db
$webhooksHandler->ping($json_content);
```
