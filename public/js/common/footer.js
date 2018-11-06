'use strict';

module.exports.show = () => {
  $('footer').append(`
    <ul class="about">
    <li><a title="Author of PCIT From Datong,Shanxi" target="_blank" href="https://www.bilibili.com/video/av34549860?from=search&seid=4430979976495551124">@PCIT, Datong, Shanxi</a></li>
    <li><a href="https://github.com/khs1994-php/pcit" target="_blank">GitHub</a></li>
    <li><a href="https:///ci.khs1994.com/wechat" target="_blank">WeChat</a></li>
    <li><a href="https://ci.khs1994.com/team" target="_blank">Team</a></li>
    <li><a href="mailto:ci@khs1994.com">Email</a></li>
  </ul>

  <ul class="help">
    <li><a href="https://docs.ci.khs1994.com" target="_blank">Documentation</a></li>
    <li><a href="https://api.ci.khs1994.com" target="_blank">API</a></li>
    <li><a href="https://ci.khs1994.com/issues" target="_blank">Community</a></li>
    <li><a href="https://ci.khs1994.com/blog" target="_blank">Blog</a></li>
    <li><a href="https://ci.khs1994.com/changelog" target="_blank">CHANGELOG</a></li>
    <li><a href="https://ci.khs1994.com/donate" target="_blank">Donate</a></li>
  </ul>

  <ul class="legal">
    <li><a href="https://ci.khs1994.com/terms-of-service" target="_blank">Terms of Service</a></li>
    <li><a href="https://ci.khs1994.com/privacy-policy" target="_blank">Privacy Policy</a></li>
    <li><a href="https://ci.khs1994.com/ce" target="_blank">PCIT CE</a></li>
    <li><a href="https://ci.khs1994.com/ee" target="_blank">PCIT EE</a></li>
    <li><a href="https://ci.khs1994.com/why" target="_blank">Why PCIT</a></li>
    <li><a href="https://ci.khs1994.com/plugins" target="_blank">Plugins</a></li>
  </ul>

  <ul class="status">
    <li><a href="https://ci.khs1994.com/status" target="_blank">PCIT Status</a></li>
  </ul>
`);
};
