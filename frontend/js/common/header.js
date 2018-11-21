module.exports.show = () => {
  //   $('header').append(`
  // <span class="ico"><img class="rounded" alt='pcit' title="PCIT IS A PHP CI TOOLKIT" id="pcit_ico" src="/ico/pcit.png"/></span>
  // <span class="docs"><a href="//docs.ci.khs1994.com" target="_blank">Documentation</a></span>
  // <span class="plugins"><a href="//docs.ci.khs1994.com/plugins/" target="_blank">Plugins</a></span>
  // <span class="donate"><a href="//zan.khs1994.com" target="_blank">Donate</a></span>
  // <span class="username">username</span>
  // `);
  $('header').append(`
    <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
    <a class="navbar-brand" href="">PCIT</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"></span>
</button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="//ci.khs1994.com/changelog">CHANGELOG</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="//docs.ci.khs1994.com">Documentation</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="//api.ci.khs1994.com">API</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://github.com/khs1994-php/pcit">GitHub</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="//ci.khs1994.com/donate">Donate</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" target="_blank" href="https://cloud.tencent.com/redirect.php?redirect=10058&cps_key=3a5255852d5db99dcd5da4c72f05df61">
          Try Kubernetes</a></div>
        </li>
    </ul>
    </div>
    </div>
  </nav>`);
};
