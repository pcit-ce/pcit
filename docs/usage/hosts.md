# hosts

该指令用来配置 hosts。

```yaml
steps:
  ping:
    run:
      - ping
      - domain.com
      - -c
      - 5

# 自定义 hosts
hosts:
  - "domain.com:127.0.0.1"
  - "git.khs1994.com:127.0.0.1"
  - "docker.khs1994.com:127.0.0.1"
```
