FROM alpine:3.11

RUN apk add --no-cache curl

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT [ "sh","/docker-entrypoint.sh" ]
