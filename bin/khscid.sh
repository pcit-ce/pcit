#!/usr/bin/env sh

dir=$(cd "${0%[/\\]*}" > /dev/null && pwd)

php ./src/khscid.php $@
