# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.3.11-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/qiniu/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/qiniu install --no-dev

FROM khs1994/php:7.2.24-fpm-alpine

COPY --from=composer /srv/pcit/plugin/qiniu/vendor/ /srv/pcit/plugin/qiniu/vendor/

COPY index.php .env.example /srv/pcit/plugin/qiniu/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/qiniu/index.php"]
