# syntax=docker/dockerfile:experimental

# @see https://laravel-news.com/multi-stage-docker-builds-for-laravel
# @see https://github.com/moby/buildkit/blob/master/frontend/dockerfile/docs/experimental.md

# ARG PHP_VERSION=7.4.12
# ARG PHP_VERSION=8.0.5
ARG PHP_VERSION=nightly
ARG NODE_VERSION=15.5.0
ARG USERNAME=khs1994

# 前端构建
FROM ${USERNAME}/node:git as frontend-builder

ARG NODE_REGISTRY=https://registry.npmjs.org

COPY frontend/package.json /app/pcit/frontend/

RUN --mount=type=cache,target=/root/.npm,id=npm_cache cd /app/pcit/frontend \
      set -x \
      # && npm install cross-env --registry=${NODE_REGISTRY} \
      # && npm install --registry=${NODE_REGISTRY} --production
      && npm install --registry=${NODE_REGISTRY}

COPY ./frontend/tsconfig.json /app/pcit/frontend/
COPY ./frontend/webpack.config.js /app/pcit/frontend/
COPY ./frontend/images /app/pcit/frontend/images
COPY ./frontend/js /app/pcit/frontend/js
COPY ./frontend/html /app/pcit/frontend/html
COPY ./frontend/css /app/pcit/frontend/css
COPY ./frontend/src /app/pcit/frontend/src

RUN set -x \
      && cd /app/pcit/frontend \
      && npm run build

# 安装 composer 依赖
FROM ${USERNAME}/php:7.4.12-composer-alpine as composer

COPY composer.json /app/pcit/
COPY src /app/pcit/src/

RUN --mount=type=cache,target=/tmp/composer/cache,id=composer_cache cd /app/pcit \
      set -x \
      && composer config -g --unset repos.packagist \
      && composer install --no-dev \
      && rm -rf src app

# 整合项目
FROM ${USERNAME}/php:${PHP_VERSION}-cli-alpine as dump

COPY --from=composer /app/pcit/vendor /app/pcit/vendor
COPY . /app/pcit
COPY --from=frontend-builder /app/pcit/public/ /app/pcit/public/

RUN set -x \
      && rm -rf /app/pcit/Dockerfile \
      && rm -rf /app/pcit/frontend \
      && rm -rf /app/pcit/.docker \
      \
      && /app/pcit/bin/pcit developer:plugin \
      && rm -rf /app/pcit/plugins

# ==> pcit
FROM --platform=$TARGETPLATFORM ${USERNAME}/php:${PHP_VERSION}-cli-alpine as pcit

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/pcit/

ENTRYPOINT ["/app/pcit/bin/pcitd"]
# ENTRYPOINT ["/app/pcit/bin/pcit"]

CMD ["up"]
# CMD ["server"]
# CMD ["agent"]

# ==> cli
FROM --platform=$TARGETPLATFORM ${USERNAME}/php:${PHP_VERSION}-cli-alpine as pcit_cli

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/pcit/

VOLUME [ "/workspace" ]

WORKDIR /workspace

ENTRYPOINT ["/app/pcit/bin/pcit"]

CMD ["list"]

# ==> fpm
FROM --platform=$TARGETPLATFORM ${USERNAME}/php:${PHP_VERSION}-fpm-alpine as pcit_fpm

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/.pcit/

COPY .docker/fpm/docker-entrypoint.sh /

ENV CI_DAEMON_ENABLED=true

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]

CMD ["up"]
# CMD ["server"]

# ==> nginx unit
FROM --platform=$TARGETPLATFORM ${USERNAME}/php:${PHP_VERSION}-unit-alpine as unit

RUN --mount=type=bind,from=khs1994/s6:2.1.0.1,source=/,target=/tmp/s6 \
    set -x \
    && tar -zxvf /tmp/s6/s6-overlay.tar.gz -C / \
# https://github.com/MinchinWeb/docker-base/commit/f5e350dcf3523a424772a1e42a3dba3200d7a2aa
    && ln -s /init /s6-init

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/pcit/

COPY .docker/unit/docker-entrypoint.sh /

COPY .docker/unit/config.json /etc/nginx-unit/

COPY .docker/unit/services.d /etc/services.d

EXPOSE 80

ENV CI_DAEMON_ENABLED=true

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]

CMD ["up"]
# CMD ["server"]

# ==> nginx unit + dockerd + pcitd (all in one)
FROM --platform=$TARGETPLATFORM docker:dind as all-in-one

COPY --from=khs1994/php:nightly-unit-alpine /usr/local/ /usr/local

COPY --from=redis:6.0.6-alpine /usr/local/bin /usr/local/bin/

RUN set -x \
#    && sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories \
    && runDeps="$( \
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
      | tr ',' '\n' \
      | sort -u \
      | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )" \
    && apk add --no-cache $runDeps \
                          tzdata \
                          bash \
                          curl \
    \
    && mkdir -p /usr/local/etc/redis /data \
    && echo > /usr/local/etc/redis/redis.conf \
    \
    && mkdir -p /var/log/nginx-unit /usr/local/nginx-unit/tmp \
    && ln -sf /dev/stdout /var/log/nginx-unit/nginx-unit.log \
    && ln -sf /dev/stdout /var/log/nginx-unit/access.log \
    \
    && php -v \
    && php -d error_reporting=22527 -d display_errors=1 -r 'var_dump(iconv("UTF-8", "UTF-8//IGNORE", "This is the Euro symbol '\''€'\''."));' \
    \
    && dockerd -v \
    \
    && unitd --version \
    \
    && redis-server -v

RUN --mount=type=bind,from=khs1994/s6:2.1.0.1,source=/,target=/tmp/s6 \
    set -x \
    && tar -zxvf /tmp/s6/s6-overlay.tar.gz -C / \
# https://github.com/MinchinWeb/docker-base/commit/f5e350dcf3523a424772a1e42a3dba3200d7a2aa
    && ln -s /init /s6-init

COPY --from=khs1994/php:nightly-composer-alpine /usr/bin/composer /usr/bin/composer

COPY .docker/unit/docker-entrypoint.sh /

COPY .docker/unit/config.json /etc/nginx-unit/

COPY .docker/unit/services.d /etc/services.d

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/pcit/

EXPOSE 80

ENV CI_DAEMON_ENABLED=true

ENV CI_REDIS_HOST=127.0.0.1

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]

CMD ["up"]
# CMD ["server"]

# dockerd
VOLUME [ "/var/lib/docker" ]
# redis-server
VOLUME [ "/data" ]
# nginx unit
VOLUME [ "/usr/local/nginx-unit/tmp", "/usr/local/nginx-unit/state" ]
# vscode remote
VOLUME [ "/root/.vscode-server", "/root/.vscode-server-insiders" ]

HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 CMD [ "curl", "-f", "127.0.0.1:80/api/healthz" ]

# ==> 前端资源
FROM --platform=$TARGETPLATFORM alpine as frontend

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/ui"

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
