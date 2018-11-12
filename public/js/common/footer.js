'use strict';

module.exports.show = () => {
  $('footer').append(`
      <div class="row">
        <div class="about col-md-3 col-sm-6 col-12">
        <div><a target="_blank" title="Author of PCIT From Datong,Shanxi"
               href="https://www.bilibili.com/video/av34549860?from=search&seid=4430979976495551124"><strong>@PCIT, Datong,
          Shanxi</strong></a></div>
        <div><a href="https://github.com/khs1994-php/pcit" target="_blank">GitHub</a></div>
        <div><a href="https://ci.khs1994.com/wechat" target="_blank">WeChat</a></div>
        <div><a href="https://ci.khs1994.com/team" target="_blank">Team</a></div>
        <div><a href="mailto:ci@khs1994.com">Email</a></div>
      </div>

      <div class="help col-md-3 col-sm-6 col-12">
        <div><strong>HELP</strong></div>
        <div><a href="https://docs.ci.khs1994.com" target="_blank">Documentation</a></div>
        <div><a href="https://api.ci.khs1994.com" target="_blank">API</a></div>
        <div><a href="https://ci.khs1994.com/issues" target="_blank">Community</a></div>
        <div><a href="https://ci.khs1994.com/blog" target="_blank">Blog</a></div>
        <div><a href="https://ci.khs1994.com/changelog" target="_blank">CHANGELOG</a></div>
        <div><a href="https://ci.khs1994.com/donate" target="_blank">Donate</a></div>
      </div>

      <div class="legal col-md-3 col-sm-6 col-12">
        <div><strong>LEGAL</strong></div>
        <div><a href="https://ci.khs1994.com/terms-of-service" target="_blank">Terms of Service</a></div>
        <div><a href="https://ci.khs1994.com/privacy-policy" target="_blank">Privacy Policy</a></div>
        <div><a href="https://ci.khs1994.com/ce" target="_blank">PCIT CE</a></div>
        <div><a href="https://ci.khs1994.com/ee" target="_blank">PCIT EE</a></div>
        <div><a href="https://ci.khs1994.com/why" target="_blank">Why PCIT</a></div>
        <div><a href="https://ci.khs1994.com/plugins" target="_blank">Plugins</a></div>
      </div>

      <div class="status col-md-3 col-sm-6 col-12">
        <div><strong>PCIT Status</strong></div>
        <div><a href="https://ci.khs1994.com/status" target="_blank">PCIT Status</a></div>
      </div>

      </div>
`);
};
