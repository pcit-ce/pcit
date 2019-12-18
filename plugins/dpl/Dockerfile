FROM khs1994/php:7.3.11-composer-alpine

RUN git clone --depth=1 https://github.com/travis-ci/dpl /dpl

FROM ruby:2.5.1-alpine

COPY --from=0 /dpl /dpl

RUN cd /dpl \
    && gem build dpl.gemspec \
    && gem install dpl-*.gem

ENTRYPOINT [ "/dpl/bin/dpl" ]
