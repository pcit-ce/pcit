FROM khs1994/php:7.2.13-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/s3/

RUN composer --working-dir=/srv/pcit/plugin/s3 install; \
      rm -rf /tmp/cache

FROM khs1994/php:7.2.13-fpm-alpine

COPY --from=composer /srv/pcit/plugin/s3/vendor/ /srv/pcit/plugin/s3/vendor/

COPY index.php .env.example /srv/pcit/plugin/s3/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/s3/index.php"]
