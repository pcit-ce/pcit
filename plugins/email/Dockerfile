FROM khs1994/php-fpm:7.2.7-alpine3.7

ENV CI_EMAIL_HOST=null \
    CI_EMAIL_USERNAME=null \
    CI_EMAIL_PASSWORD=null \
    CI_EMAIL_FROM_NAME=null \
    CI_EMAIL_ADDRESS_JSON=null \
    CI_EMAIL_CC_JSON=null \
    CI_EMAIL_BCC_JSON=null \
    CI_EMAIL_OBJECT=null \
    CI_EMAIL_BODY=null

WORKDIR /khsci

COPY composer.json index.php .env.example /khsci/

RUN composer install \
    && rm -rf \tmp\cache

ENTRYPOINT ["php","index.php"]

CMD send
