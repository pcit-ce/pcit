module.exports.show = () => {
  $('footer').append(`
    <ul class="about">
    <li>@PCIT, Datong, Shanxi</li>
    <li><a href="https://github.com/khs1994-php/pcit" target="_blank">GitHub</a></li>
    <li><a href="//weibo.com/kanghuaishuai" target="_blank">Weibo</a></li>
    <li><a href="/wechat" target="_blank">WeChat</a></li>
    <li><a href="//shang.qq.com/wpa/qunwpa?idkey=776defd7c271e9de70b9dfae855a34f11aada1fec9f27d22303dfffcb6d75e63" target="_blank">QQ Group</a></li>
    <li><a href="mailto:ci@khs1994.com">Email</a></li>
  </ul>

  <ul class="help">
    <li><a href="https://github.com/khs1994-php/pcit/tree/master/docs" target="_blank">Documentation</a></li>
    <li><a href="//api.ci.khs1994.com" target="_blank">API</a></li>
    <li><a href="https://github.com/khs1994-php/pcit/issues" target="_blank">Community</a></li>
    <li><a href="/blog" target="_blank">Blog</a></li>
    <li><a href="https://github.com/khs1994-php/pcit/blob/master/CHANGELOG.md" target="_blank">CHANGELOG</a></li>
    <li><a href="https://zan.khs1994.com" target="_blank">Donate</a></li>
  </ul>

  <ul class="legal">
    <li><a href="" target="_blank">Terms of Service</a></li>
    <li><a href="" target="_blank">Privacy Policy</a></li>
    <li><a href="https://github.com/khs1994-php/pcit/blob/master/docs/install/ce.md" target="_blank">PCIT CE</a></li>
    <li><a href="https://github.com/khs1994-php/pcit/blob/master/docs/install/ee.md" target="_blank">PCIT EE</a></li>
    <li><a href="https://github.com/khs1994-php/pcit/blob/master/docs/why.md" target="_blank">Why PCIT</a></li>
    <li><a href="//api.ci.khs1994.com" target="_blank">Plugins</a></li>
  </ul>

  <ul class="status">
    <li><a href="" target="_blank">PCIT Status</a></li>
  </ul>
`
  );
};
