FROM khs1994/php-fpm:7.2.7-alpine3.7

ENV COS_V5_APP_ID=null \
    COS_V5_REGION=null \
    COS_V5_KEY=null \
    COS_V5_SECRET=null \
    COS_V5_BUCKET=null \
    COS_V5_LABEL=null \
    COS_V5_FILE=null

WORKDIR /khsci

COPY composer.json index.php .env.example /khsci/

RUN composer install \
    && rm -rf \tmp\cache

ENTRYPOINT ["php","index.php"]

CMD upload
