# WebHooks

* https://developer.github.com/webhooks/
* https://developer.github.com/v3/activity/events/types/

## Handler

PCITDaemon 处理 Webhooks 数据，存储到数据库。

```php
$webhooksHandler = new \PCIT\GitHub\Webhooks\Handler\Kernel();

// save data to db
$webhooksHandler->ping($webhooks_content);
```

## Parser

解析 Webhooks 数据

## Server

接收 Webhooks 数据
