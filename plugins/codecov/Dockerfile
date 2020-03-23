FROM alpine

RUN set -x \
    && apk add --no-cache bash curl findutils \
    && curl -o /usr/local/bin/codecov https://codecov.io/bash

ENTRYPOINT [ "bash", "/usr/local/bin/codecov" ]
