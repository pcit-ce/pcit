# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.4.3-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/demo-use-toolkit/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/demo-use-toolkit install --no-dev

FROM khs1994/php:7.4.3-cli-alpine

COPY --from=composer /srv/pcit/plugin/demo-use-toolkit/vendor/ /srv/pcit/plugin/demo-use-toolkit/vendor/

COPY index.php .env.example /srv/pcit/plugin/demo-use-toolkit/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/demo-use-toolkit/index.php"]
