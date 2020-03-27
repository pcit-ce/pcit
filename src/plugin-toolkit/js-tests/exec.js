(async()=>{
const exec = require('@actions/exec');

await exec.exec('node', ['index.js', 'foo=bar']);
})()
