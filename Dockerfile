# syntax=docker/dockerfile:experimental

# @see https://laravel-news.com/multi-stage-docker-builds-for-laravel
# @see https://github.com/moby/buildkit/blob/master/frontend/dockerfile/docs/experimental.md

ARG PHP_VERSION=7.4.2
ARG NODE_VERSION=13.8.0

# 前端构建
FROM khs1994/node:git as frontend-builder

ARG NODE_REGISTRY=https://registry.npmjs.org

COPY frontend/package.json /app/pcit/frontend/

RUN --mount=type=cache,target=/root/.npm,id=npm_cache cd /app/pcit/frontend \
      # && npm install cross-env --registry=${NODE_REGISTRY} \
      # && npm install --registry=${NODE_REGISTRY} --production
      && npm install --registry=${NODE_REGISTRY}

COPY ./frontend/webpack.config.js /app/pcit/frontend/
COPY ./frontend/images /app/pcit/frontend/images
COPY ./frontend/js /app/pcit/frontend/js
COPY ./frontend/html /app/pcit/frontend/html
COPY ./frontend/css /app/pcit/frontend/css
COPY ./frontend/src /app/pcit/frontend/src

RUN cd /app/pcit/frontend \
      && npm run build

# 安装 composer 依赖
FROM khs1994/php:7.4.2-composer-alpine as composer

COPY composer.json /app/pcit/
COPY src /app/pcit/src/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache cd /app/pcit \
      && composer install --no-dev \
      && rm -rf src

# 整合项目
FROM alpine as dump

COPY --from=composer /app/pcit/vendor /app/pcit/vendor
COPY . /app/pcit
COPY --from=frontend-builder /app/pcit/public/ /app/pcit/public/

RUN rm -rf /app/pcit/Dockerfile \
    && rm -rf /app/pcit/frontend \
    && rm -rf /app/pcit/.docker

# pcit
FROM khs1994/php:${PHP_VERSION}-fpm-alpine as pcit

COPY --from=dump /app/pcit/ /app/pcit/

ENTRYPOINT ["/app/pcit/bin/pcitd"]
# ENTRYPOINT ["/app/pcit/bin/pcit"]

CMD ["up"]
# CMD ["server"]
# CMD ["agent"]

# nginx unit

FROM khs1994/php:7.4.2-unit-alpine as unit

COPY --from=dump /app/pcit/ /app/pcit/

COPY .docker/unit/docker-entrypoint.sh /

COPY .docker/unit/config.json /etc/nginx-unit/

EXPOSE 80

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]

CMD ["up"]
# CMD ["server"]

# 前端资源
FROM alpine as frontend

COPY --from=dump /app/pcit/public/ /app/pcit/public/

VOLUME /var/www/pcit/public

CMD ["cp","-r","/app/pcit/public/","/var/www/pcit/"]

#
# $ docker buildx build -t pcit/pcit --target=pcit .
#
# $ docker buildx build -t pcit/pcit --target=frontend .
#
# @link https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
#
