# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.4.12-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/qiniu/

RUN --mount=type=cache,target=/tmp/composer/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/qiniu install --no-dev

FROM khs1994/php:7.4.12-cli-alpine

COPY --from=composer /srv/pcit/plugin/qiniu/vendor/ /srv/pcit/plugin/qiniu/vendor/

COPY index.php .env.example /srv/pcit/plugin/qiniu/

ENTRYPOINT ["php","-d","memory_limit=500M"]

CMD ["/srv/pcit/plugin/qiniu/index.php"]
