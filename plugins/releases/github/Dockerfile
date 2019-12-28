FROM alpine:3.11

RUN apk add --no-cache ca-certificates

COPY --from=plugins/github-release /bin/drone-github-release /bin/drone-github-release
COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["sh","/docker-entrypoint.sh"]
