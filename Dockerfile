# @see https://laravel-news.com/multi-stage-docker-builds-for-laravel

ARG PHP_VERSION=7.2.12
ARG NODE_VERSION=11.1.0

# 安装前端构建依赖
FROM node:${NODE_VERSION}-alpine as frontend

ARG NODE_REGISTRY=https://registry.npmjs.org

COPY frontend/package.json /app/pcit/frontend/

RUN cd /app/pcit/frontend \
      && npm install cross-env --registry=${NODE_REGISTRY} \
      && npm install --registry=${NODE_REGISTRY} --production

COPY ./frontend/webpack.config.js /app/pcit/frontend/
COPY ./frontend/js /app/pcit/frontend/js
COPY ./frontend/html /app/pcit/frontend/html
COPY ./frontend/css /app/pcit/frontend/css

RUN cd /app/pcit/frontend \
      # && set PATH=./node_modules/.bin:$PATH \
      && npm run build

# 安装 composer 依赖
FROM khs1994/php:7.2.12-composer-alpine as composer

COPY composer.json /app/pcit/

RUN cd /app/pcit \
      && composer install --no-dev

# 将 PHP 项目打入 PHP 镜像
FROM khs1994/php:${PHP_VERSION}-fpm-alpine as php

COPY . /app/pcit
COPY --from=composer /app/pcit/vendor /app/pcit/vendor
COPY --from=frontend /app/pcit/public/ /app/pcit/public/

CMD ["/app/pcit/bin/pcitd", "up"]

#
# $ docker build -t khs1994/pcit --target=php .
#
# @link https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
#
