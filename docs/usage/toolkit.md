# toolkit

你可以使用一些位于 `/opt/pcit/toolkit/pcit-CMD` 的工具命令，例如 `pcit-retry` 支持重试。

详情请到 https://github.com/pcit-ce/toolkit 查看

```yaml
steps:
  use_toolkit: /opt/pcit/toolkit/pcit-retry -t 2 --sleep 5 'echo "y u no work"; false'
```
