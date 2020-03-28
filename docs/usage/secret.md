# secret

一些密钥可通过设置页面添加，例如 **密码**

当密钥的值为文件时，可通过 CLI 的 `encrypt-file` 命令输出格式化后的值，并将文件的第一行添加到某个私密变量中：

```bash
PRIVATE_FILE_CONTENT=xxx1\nxxx\n
PRIVATE_FILE_CONTENT_FIRST_LINE=xxx1
```

在使用时执行 `echo -e ${PRIVATE_FILE_CONTENT} > private.key` 输出到文件中。

```yaml
steps:
  script:
    run: |
      echo -e ${PRIVATE_FILE_CONTENT} > private.key

      # 执行 cat 命令会泄露文件内容，为避免泄露必须将文件第一行内容设置为某个密钥的值
      cat private.key
```
