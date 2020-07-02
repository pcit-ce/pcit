command = require('@actions/core/lib/command');

command.issue('name','message % \r \n : ,');
command.issueCommand('name',{"a":"value % \r \n : ,"},'message');
console.log(command.toCommandValue('name  % \r \n : ,'));
console.log(command.toCommandValue({"a":"value  % \r \n : ,"}));
