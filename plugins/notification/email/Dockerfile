FROM khs1994/php:7.2.10-fpm-alpine

COPY composer.json /srv/pcit/plugin/email/

RUN composer --working-dir=/srv/pcit/plugin/email install \
    && rm -rf /tmp/cache

COPY index.php .env.example /srv/pcit/plugin/email/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/email/index.php"]
