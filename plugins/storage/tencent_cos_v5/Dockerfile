# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.4.2-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/cosv5/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/cosv5 install --no-dev

FROM khs1994/php:7.2.24-fpm-alpine

COPY --from=composer /srv/pcit/plugin/cosv5/vendor/ /srv/pcit/plugin/cosv5/vendor/

COPY index.php .env.example /srv/pcit/plugin/cosv5/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/cosv5/index.php"]
