# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.3.4-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/email/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/email install

FROM khs1994/php:7.2.15-fpm-alpine

COPY --from=composer /srv/pcit/plugin/email/vendor/ /srv/pcit/plugin/email/vendor/

COPY index.php .env.example /srv/pcit/plugin/email/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/email/index.php"]
