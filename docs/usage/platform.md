# platform

`platform` 指令只对 `steps` 有效，对 `services` 无效。与 `docker run --platform` 参数的作用一致。

```yaml
platform:
  - linux/amd64
  - linux/arm64
  - linux/ppc64le
  - linux/s390x
  - linux/386
  - linux/arm/v7
  - linux/arm/v6
```
