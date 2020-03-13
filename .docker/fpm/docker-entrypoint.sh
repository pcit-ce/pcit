#!/usr/bin/env sh

php-fpm -D --pid /var/run/php-fpm.pid

if [ "${CI_DAEMON_ENABLED}" = 'false' ];then
  FPM_PID=`cat /var/run/php-fpm.pid`
  kill $UNIT_PID

  exec php-fpm -F
fi

exec /app/pcit/bin/pcitd $@
