$('header').append(`<span class="ico"><img title="PCIT IS A PHP CI TOOLKIT" id="pcit_ico" src="/ico/pcit.png"/></span>
<span class="docs"><a href="//docs.ci.khs1994.com" target="_blank">Documentation</a></span>
<span class="plugins"><a href="//docs.ci.khs1994.com/plugins/" target="_blank">Plugins</a></span>
<span class="donate"><a href="//zan.khs1994.com" target="_blank">Donate</a></span>
<span class="username">username</span>`
);

$('footer').append(`<ul class="about">
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

let url_array = location.href.split('/');
let username = url_array[4];
let git_type = url_array[3];

$(document).ready(function () {
  $.ajax({
      type: 'GET',
      url: '/api/repos/' + `${git_type}/${username}`,
      success: function (data) {
        $('.username_header').append(`${git_type} || ${username}`);

        repo(data);
      }
    }
  )
});

function repo(data) {
  $.each(data, function ($k, $v) {
    let {repo_full_name} = $v;

    $.ajax({
      type: 'GET',
      url: '/api/repo/' + `${git_type}/${repo_full_name}/build/current`,
      success: function (data) {

        console.log(data);

        let {
          id: last_build_id,
          branch: last_build_branch,
          commit_id: last_build_commit,
          created_at: time
        } = data;

        last_build_commit = last_build_commit.slice(0, 7);
        let repo_name = repo_full_name.split('/');

        $('.repo').append(`
<tr>
<td class="repo_full_name">${repo_name[1]}</td>
<td class="last_build_id">${last_build_id}</td>
<td class="last_build_branch">${last_build_branch}
<td class="last_build_commit">${last_build_commit}</td>
<td class="time">${time}</td>
</tr>      
        `);
      }
    });
  });
}
