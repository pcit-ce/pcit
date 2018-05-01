<?php

declare(strict_types=1);
if ($argc < 4) {
    $array = array_filter(scandir(__DIR__), function ($k) {
        if (in_array($k, ['.', '..'])) {
            return false;
        }

        return true;
    });

    var_dump($array);

    echo '
Usages:

php migrations.php MYSQL_USER MYSQL_PASSWORD SQL_FILE

';
    exit;
}

$output = popen('mysql -u'.$argv[1].' -p'.$argv[2].' < '.$argv[3], 'r');

var_dump(stream_get_contents($output));
