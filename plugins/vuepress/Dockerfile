FROM node:alpine

RUN set -x \
    && npm -g i vuepress \
    && rm -rf ~/.npm ~/.config

COPY docker-entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["sh","/docker-entrypoint.sh"]
