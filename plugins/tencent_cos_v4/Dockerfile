FROM khs1994/php-fpm:7.2.6-alpine3.7

ENV COS_REGION=null \
    COS_APP_ID=null \
    COS_SECRET_ID=null \
    COS_SECRET_KEY=null \
    COS_BUCKET=null \
    COS_LABEL=null \
    COS_FILE=null

WORKDIR /khsci

COPY composer.json index.php .env.example /khsci/

RUN composer install \
    && rm -rf \tmp\cache

ENTRYPOINT ["php","index.php"]

CMD upload
