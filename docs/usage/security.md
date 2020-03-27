# security

## 保护文件

例如有如下的私密文件

```bash
Csm9Z3f9yKSh+dQaiObcI6dq7bSh+1RcHR0ZIZMq2dNLwobN/WFGPtrOydKa2xQ3
6puio9oP3hFwloQ69GT+CsyydKSSe4ABZZzxeQIDAQABAoIBAEbLaeheqCfjenwx
```

你可以将文件的第一行 `Csm9Z3f9yKSh+dQaiObcI6dq7bSh+1RcHR0ZIZMq2dNLwobN/WFGPtrOydKa2xQ3`
在设置中设置为某变量的值，防止被其他人使用 `cat file_name` 获取到文件内容。
