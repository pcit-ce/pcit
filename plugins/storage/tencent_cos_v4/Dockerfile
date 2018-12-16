FROM khs1994/php:7.2.13-composer-alpine as composer

COPY composer.json /srv/pcit/plugin/cosv4/

RUN composer --working-dir=/srv/pcit/plugin/cosv4 install \
      && rm -rf /tmp/cache

FROM khs1994/php:7.2.13-fpm-alpine

COPY --from=composer /srv/pcit/plugin/cosv4/vendor/ /srv/pcit/plugin/cosv4/vendor/

COPY index.php .env.example /srv/pcit/plugin/cosv4/

ENTRYPOINT ["php"]

CMD ["/srv/pcit/plugin/cosv4/index.php"]
