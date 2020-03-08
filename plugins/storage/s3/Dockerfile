# syntax=docker/dockerfile:experimental
FROM khs1994/php:7.4.3-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/s3/

RUN --mount=type=cache,target=/tmp/cache,id=composer_cache \
    composer --working-dir=/srv/pcit/plugin/s3 install --no-dev

FROM khs1994/php:7.4.3-cli-alpine

COPY --from=composer /srv/pcit/plugin/s3/vendor/ /srv/pcit/plugin/s3/vendor/

COPY index.php .env.example /srv/pcit/plugin/s3/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/s3/index.php"]
