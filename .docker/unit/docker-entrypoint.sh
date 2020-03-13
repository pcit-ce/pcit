#!/usr/bin/env sh

set -x

unitd --user root --group root --log /var/log/nginx-unit/nginx-unit.log

set +x
cd /etc/nginx-unit
set -x

curl -X PUT --data-binary @config.json --unix-socket \
       /usr/local/nginx-unit/control.unit.sock \
       http://localhost/config

set +x
cd /app/pcit
set -x

if [ "${CI_DAEMON_ENABLED}" = 'false' ];then
  UNIT_PID=`cat /usr/local/nginx-unit/unit.pid`
  kill $UNIT_PID

  exec unitd --no-daemon --user root --group root --log /var/log/nginx-unit/nginx-unit.log
fi

exec /app/pcit/bin/pcitd $@
