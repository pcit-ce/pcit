FROM khs1994/php-fpm:7.2.7-alpine3.7

ENV COS_V4_REGION=null \
    COS_V4_APP_ID=null \
    COS_V4_SECRET_ID=null \
    COS_V4_SECRET_KEY=null \
    COS_V4_BUCKET=null \
    COS_V4_LABEL=null \
    COS_V4_FILE=null

WORKDIR /pcit

COPY composer.json index.php .env.example /pcit/

RUN composer install \
    && rm -rf \tmp\cache

ENTRYPOINT ["php","index.php"]

CMD upload
