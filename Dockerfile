#
# @see https://laravel-news.com/multi-stage-docker-builds-for-laravel
#

ARG PHP_VERSION=7.2.11-fpm-alpine
ARG NODE_VERSION=alpine

#
# 前端构建
#

FROM node:${NODE_VERSION} as frontend

COPY . /app/pcit

RUN cd /app/pcit/public/public \
      && mkdir -p /app/pcit/public/storage/private_key \
      && npm install -g cross-env \
      && npm install \
      && npm run build \
      && rm -rf node_modules

#
# 安装 composer 依赖
#

FROM khs1994/php:${PHP_VERSION} as composer

COPY --from=frontend /app/pcit /app/pcit

RUN cd /app/pcit \
      #
      # 安装 composer 依赖
      #
      && if [ -f composer.json ];then \
           echo "Composer packages installing..."; \
           composer install --no-dev; \
           echo "Composer packages install success"; \
         else \
           echo "composer.json NOT exists"; \
         fi

#
# 将 PHP 项目打入 PHP 镜像
#

FROM khs1994/php:${PHP_VERSION} as php

COPY --from=composer /app /app

CMD ["/app/pcit/bin/pcitd", "up"]

#
# $ docker build -t khs1994/pcit --target=php .
#
# @link https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
#
