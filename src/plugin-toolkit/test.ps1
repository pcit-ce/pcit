php php-tests/core.php > php-test.txt

node js-tests/core.js > js-test.txt

wsl -d debian diff php-test.txt js-test.txt
