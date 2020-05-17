# syntax=docker/dockerfile:experimental

# @see https://laravel-news.com/multi-stage-docker-builds-for-laravel
# @see https://github.com/moby/buildkit/blob/master/frontend/dockerfile/docs/experimental.md

ARG PHP_VERSION=7.4.6
ARG NODE_VERSION=14.2.0
ARG USERNAME=khs1994

# 前端构建
FROM ${USERNAME}/node:git as frontend-builder

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
FROM ${USERNAME}/php:7.4.6-composer-alpine as composer

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
FROM --platform=$TARGETPLATFORM ${USERNAME}/php:7.4.6-unit-alpine as unit

ARG VCS_REF="unknow"
ARG UI_VCS_REF="unknow"

LABEL org.opencontainers.image.revision=$VCS_REF \
      ui.revision=$UI_VCS_REF \
      org.opencontainers.image.source="https://github.com/pcit-ce/pcit"

COPY --from=dump /app/pcit/ /app/pcit/

COPY .docker/unit/docker-entrypoint.sh /

COPY .docker/unit/config.json /etc/nginx-unit/

EXPOSE 80

ENV CI_DAEMON_ENABLED=true

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]

CMD ["up"]
# CMD ["server"]

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
