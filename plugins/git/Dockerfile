FROM plugins/base:linux-amd64

RUN apk add --no-cache git openssh curl perl

COPY --from=plugins/git /bin/drone-git /bin/
ENTRYPOINT ["/bin/drone-git"]
