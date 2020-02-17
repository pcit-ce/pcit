#!/usr/bin/env sh

unitd --user root --group root --log /var/log/nginx-unit/nginx-unit.log

cd /etc/nginx-unit

curl -X PUT --data-binary @config.json --unix-socket \
       /usr/local/nginx-unit/control.unit.sock \
       http://localhost/config

cd /app/pcit

exec /app/pcit/bin/pcitd $@
