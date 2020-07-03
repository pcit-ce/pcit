<?php

declare(strict_types=1);

use PCIT\Plugin\Toolkit\Core;

require __DIR__.'/../../../vendor/autoload.php';

// require __DIR__.'/../vendor/autoload.php';

$core = new Core();

$core->addPath('/my/path');

$core->debug("debug % \r \n : ,");
$core->info("info % \r \n : ,");
$core->warning("warning % \r \n : ,");
$core->error("error % \r \n : ,");

$core->startGroup("group % \r \n : ,");
$core->endGroup();

$core->exportVariable('var', "value % \r \n : ,");

$core->setOutput('output', "value % \r \n : ,");
$core->exportVariable('INPUT_VAR', "value % \r \n : ,");
print_r($core->getInput('var'));
echo "\n";

$core->saveState('state', "value % \r \n : ,");

$core->exportVariable('STATE_STATE', "value % \r \n : ,");
print_r($core->getState('state'));
echo "\n";

print_r(false === $core->isDebug() ? 'false' : 'true');
echo "\n";
echo '::echo::off'."\n";
echo '::error::failed %25 %0D %0A : ,'."\n";
$core->setSecret("secret % \r \n : ,");
