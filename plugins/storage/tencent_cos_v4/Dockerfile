FROM khs1994/php:7.2.12-fpm-alpine

COPY composer.json /srv/pcit/plugin/cosv4/

RUN composer --working-dir=/srv/pcit/plugin/cosv4 install \
      && rm -rf /tmp/cache

COPY index.php .env.example /srv/pcit/plugin/cosv4/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/cosv4/index.php"]
