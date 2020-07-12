FROM alpine

ENV PCIT_TOOLKIT_ROOT=/var/lib/pcit/toolkit

RUN set -x \
    && apk add --no-cache curl \
    && mkdir -p ${PCIT_TOOLKIT_ROOT} \
    \
    && curl -k -o ${PCIT_TOOLKIT_ROOT}/pcit-retry https://raw.githubusercontent.com/kadwanev/retry/master/retry \
    \
    && curl -k -o ${PCIT_TOOLKIT_ROOT}/pcit-wait-for-it https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    \
    && chmod -R +x ${PCIT_TOOLKIT_ROOT}/.

VOLUME [ "/data" ]

ENTRYPOINT [ "cp", "-a", "/var/lib/pcit/toolkit/.", "/data" ]
