# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.4.12-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/cosv5/

RUN --mount=type=cache,target=/tmp/composer/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/cosv5 install --no-dev

FROM khs1994/php:7.4.12-cli-alpine

COPY --from=composer /srv/pcit/plugin/cosv5/vendor/ /srv/pcit/plugin/cosv5/vendor/

COPY index.php .env.example /srv/pcit/plugin/cosv5/

ENTRYPOINT ["php","-d","memory_limit=500M"]

CMD ["/srv/pcit/plugin/cosv5/index.php"]
