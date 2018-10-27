# 在 GitHub App 页面下载私钥

# 将私钥改名

```bash
$ mv pcit.2018-06-13.private-key.pem private.key
```

# 由私钥生成公钥

```bash
$ openssl rsa -in private.key -pubout -out public.key
```
