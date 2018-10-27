module.exports.show = () => {
  $('header').append(`
<span class="ico"><img alt='pcit' title="PCIT IS A PHP CI TOOLKIT" id="pcit_ico" src="/ico/pcit.png"/></span>
<span class="docs"><a href="//docs.ci.khs1994.com" target="_blank">Documentation</a></span>
<span class="plugins"><a href="//docs.ci.khs1994.com/plugins/" target="_blank">Plugins</a></span>
<span class="donate"><a href="//zan.khs1994.com" target="_blank">Donate</a></span>
<span class="username">username</span>
`
  );
};
