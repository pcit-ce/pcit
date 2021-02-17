# networks

该指令用来配置网络。

```yaml
steps:
  ping:
    run:
      - ping
      - domain.com
      - -c
      - 5

# 网络相关配置
networks:
  - host
```
