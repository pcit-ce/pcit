const core = require('@actions/core');
const process = require('process');

core.addPath('/my/path');

core.debug('debug % \r \n : ,');
core.info('info % \r \n : ,');
core.warning('warning % \r \n : ,');
core.error('error % \r \n : ,');

core.startGroup('group % \r \n : ,');
core.endGroup();

core.exportVariable('var','value % \r \n : ,');

core.setOutput('output','value % \r \n : ,');
core.exportVariable('INPUT_VAR','value % \r \n : ,');
console.log(core.getInput('var'));

core.saveState('state','value % \r \n : ,');

core.exportVariable('STATE_STATE','value % \r \n : ,');
console.log(core.getState('state'));
// core.group();

console.log(core.isDebug());
core.setCommandEcho();
core.setFailed('failed % \r \n : ,');

core.setSecret('secret % \r \n : ,');
