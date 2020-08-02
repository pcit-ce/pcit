**放置私钥 private.key**

在 GitHub App 页面下载私钥(例如 `pcit.2018-06-13.private-key.pem`)

将私钥改名为 `private.key` 放置到此文件夹

**由私钥生成公钥**

无需生成公钥，这里仅是列出方法，PCIT 会自动的根据私钥生成公钥。

```bash
$ openssl rsa -in private.key -pubout -out public.key
```
