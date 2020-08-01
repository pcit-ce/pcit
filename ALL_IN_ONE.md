# all-in-one docker image

```bash
$ docker network create pcit-all-in-one
```

```bash
$ docker run -it --rm \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=pcit \
  -v pcit-all-in-one-mysql-data:/var/lib/mysql \
  --name pcit-all-in-one-mysql \
  --network pcit-all-in-one \
  mysql:8.0.21
```

```bash
$ docker run -it --rm \
  --privileged \
  -e CI_MYSQL_HOST=pcit-all-in-one-mysql \
  -p 8080:80 \
  --network pcit-all-in-one \
  pcit/all-in-one

  # -v pcit-all-in-one-dockerd-data:/var/lib/docker \
  # -v pcit-all-in-one-redis-data:/data \
```
