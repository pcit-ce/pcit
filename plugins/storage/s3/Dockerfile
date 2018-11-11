FROM khs1994/php:7.2.12-fpm-alpine

COPY composer.json /srv/pcit/plugin/s3/

RUN composer --working-dir=/srv/pcit/plugin/s3 install; \
      rm -rf /tmp/cache

COPY index.php .env.example /srv/pcit/plugin/s3/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/s3/index.php"]
