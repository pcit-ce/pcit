FROM docker

ENV BUILDX_VERSION=0.4.1

RUN set -x \
    && apk add --no-cache curl ca-certificates \
    && mkdir -p ~/.docker/cli-plugins \
    && curl -fsSL https://github.com/docker/buildx/releases/download/v${BUILDX_VERSION}/buildx-v${BUILDX_VERSION}.linux-amd64 \
    -o ~/.docker/cli-plugins/docker-buildx \
    && chmod +x ~/.docker/cli-plugins/docker-buildx \
    && apk del --no-network curl ca-certificates \
    && apk add --no-cache bash

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["bash","/docker-entrypoint.sh"]
