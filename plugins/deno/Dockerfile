FROM alpine as downloader

RUN set -x \
    && apk add --no-cache curl unzip

ARG DENO_VERSION=1.7.4

ENV DENO_VERSION=${DENO_VERSION}

RUN set -x \
    && curl -L -o /tmp/deno.zip https://github.com/denoland/deno/releases/download/v${DENO_VERSION}/deno-x86_64-unknown-linux-gnu.zip \
    && unzip -d /usr/local/bin /tmp/deno.zip \
    && rm -rf /tmp/deno.zip \
    && chmod +x /usr/local/bin/deno

FROM frolvlad/alpine-glibc:alpine-3.13_glibc-2.32

COPY --from=downloader /usr/local/bin/deno /usr/local/bin/deno

ENV DENO_DIR /root/.deno

RUN set -x \
    && addgroup -g 1000 -S deno \
    && adduser -u 1000 -S deno -G deno

VOLUME [ "/root/.deno" ]

ENTRYPOINT [ "deno" ]

CMD ["https://deno.land/std/examples/welcome.ts"]
