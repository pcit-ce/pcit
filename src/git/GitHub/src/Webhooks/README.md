# WebHooks

## Handler

PCITDaemon 处理 Webhooks 数据，存储到数据库。

```php
$webhooksHandler = new \PCIT\GitHub\Webhooks\Handler\Kernel();

// save data to db
$webhooksHandler->ping($json_content);
```

## Parse

解析 Webhooks 数据

## Server

接收 Webhooks 数据
