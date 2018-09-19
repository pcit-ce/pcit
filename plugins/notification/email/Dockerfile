FROM khs1994/php:7.2.10-fpm-alpine

ENV CI_EMAIL_HOST=null \
    CI_EMAIL_USERNAME=null \
    CI_EMAIL_PASSWORD=null \
    CI_EMAIL_FROM_NAME=null \
    CI_EMAIL_ADDRESS_JSON=null \
    CI_EMAIL_CC_JSON=null \
    CI_EMAIL_BCC_JSON=null \
    CI_EMAIL_OBJECT=null \
    CI_EMAIL_BODY=null

WORKDIR /pcit

COPY composer.json index.php .env.example /pcit/

RUN composer install \
    && rm -rf \tmp\cache

ENTRYPOINT ["php","index.php"]

CMD send
