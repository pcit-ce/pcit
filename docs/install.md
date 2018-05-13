
# KhsCI EE 2019.05.01 Available

# KhsCI CE 2018.10.01 Available

* 安装好 Docker 并克隆部署环境（LNMP）

```bash
$ git clone https://github.com/khs1994-docker/lnmp

$ cd lnmp
 
$ ./lnmp-docker.sh khsci-init
```

* 准备网站 TLS 证书，放入 `config/nginx/ssl`

* 配置 NGINX，编辑 `config/nginx/khsci.conf`

* 在 GitHub 网站新建 GitHub App

* 编辑 `app/khsci/public/.env.development` 文件，设置好相关变量

* 启动

```bash
$ lnmp-docker.sh khsci-up
```

Open you Browsers, Try it!
