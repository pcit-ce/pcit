# Docker Daemon With TLS

* https://www.khs1994.com/docker/dockerd.html

配置好 Docker Daemon 之后，将以下文件放到 `public/private_key` 文件夹中

`ca.pem` `cert.pem` `key.pem`

将 `public/.env.*` 中 `CI_DOCKER_TLS_VERIFY` 的变量值改为 `true`

`CI_DOCKER_TLS_VERIFY=true`
