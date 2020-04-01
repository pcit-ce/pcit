# secret

一些密钥可通过设置页面添加，例如 **密码**

在使用时执行 `echo -e ${PRIVATE_FILE_CONTENT} > private.key` 输出到文件中。

```yaml
steps:
  script:
    run: |
      echo -e ${PRIVATE_FILE_CONTENT} > private.key
```
