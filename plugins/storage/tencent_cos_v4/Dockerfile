# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.3.4-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/cosv4/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/cosv4 install --no-dev

FROM khs1994/php:7.2.15-fpm-alpine

COPY --from=composer /srv/pcit/plugin/cosv4/vendor/ /srv/pcit/plugin/cosv4/vendor/

COPY index.php .env.example /srv/pcit/plugin/cosv4/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/cosv4/index.php"]
