FROM alpine:3.11

COPY --from=plugins/github-release /bin/drone-github-release /bin/drone-github-release
COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["sh","/docker-entrypoint.sh"]
