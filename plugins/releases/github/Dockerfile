FROM alpine:3.11

COPY docker-entrypoint.sh /docker-entrypoint.sh
COPY --from=plugins/github-release /bin/drone-github-release /bin/drone-github-release

ENTRYPOINT ["sh","/docker-entrypoint.sh"]
