<?php

declare(strict_types=1);

use PCIT\Plugin\Toolkit\Core;

require 'vendor/autoload.php';

$core = new Core();

$core->getInput('inputName', $required ?? false);
$core->setOutput('outputKey', 'outputVal');

$core->exportVariable('envVar', 'Val');

$core->setSecret('myPassword');

$core->addPath('/path/to/mytool');

$core->isDebug();
$core->debug('Inside try block');
$core->warning('myInput was not set');
$core->error('Error, action may still succeed though');

$core->saveState('pidToKill', '12345');
$pid = $core->getState('pidToKill');

$core->setFailed('Action failed with error');
