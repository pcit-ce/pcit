FROM alpine:3.12

RUN apk add --no-cache ca-certificates curl

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["sh","/docker-entrypoint.sh"]
