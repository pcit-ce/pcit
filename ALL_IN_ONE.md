# all-in-one docker image

**1. create docker network**

```bash
$ docker network create pcit-all-in-one
```

**2. run mysql container**

```bash
$ docker run -it \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=pcit \
  -v pcit-all-in-one_mysql-data:/var/lib/mysql \
  --name pcit-all-in-one-mysql \
  --network pcit-all-in-one \
  -d \
  mysql:8.0.21
```

**3. run pcit all-in-one container**

```bash
$ docker run -it \
  --privileged \
  -e CI_MYSQL_HOST=pcit-all-in-one-mysql \
  -p 8080:80 \
  --network pcit-all-in-one \
  --name pcit-all-in-one \
  -d \
  pcit/all-in-one

  # -v pcit-all-in-one_tmp-data:/tmp \
  # -v pcit-all-in-one_dockerd-data:/var/lib/docker \
  # -v pcit-all-in-one_redis-data:/data \
  # -v /path/to/redis.conf:/usr/local/etc/redis/redis.conf \
  # -v /path/to/daemon.json:/etc/docker/daemon.json \
  # -v /path/to/docker-php.ini:/usr/local/etc/php/conf.d/docker-php.ini \
  # -v /path/to/.env:/app/pcit/.env \

  # -v pcit-all-in-one_vscode-data:/root/.vscode-server \
  # -v pcit-all-in-one_vscode-insiders-data:/root/.vscode-server-insiders \
```

**docker-compose**

please see [pcit-all-in-one](pcit-all-in-one)
