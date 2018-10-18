$('header').append(`
<span class="ico"><img alt='ico' title="PCIT IS A PHP CI TOOLKIT" id="pcit_ico" src="/ico/pcit.png"/></span>
<span class="docs"><a href="//docs.ci.khs1994.com" target="_blank">Documentation</a></span>
<span class="plugins"><a href="//docs.ci.khs1994.com/plugins/" target="_blank">Plugins</a></span>
<span class="donate"><a href="//zan.khs1994.com" target="_blank">Donate</a></span>
<span class="username">username</span>
`
);

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

let ci_host = "https://" + location.host + "/";

let url_array = location.href.split('/');

let git_type = url_array[4];

let username = url_array[5];

function formatGitType(gittype) {
  switch (gittype) {
    case 'github':
      return 'GitHub';

    default:
      return gittype.substring(0, 1).toUpperCase() + gittype.substring(1);
  }
}

function showUserBasicInfo(data) {
  $("#username").text(data.username).addClass(data.type);

  $("title").text(`${formatGitType(git_type)} - ${data.username} - Profile - PCIT`);

  $("#user").empty().append(
    "<span onclick='click_user()'>" + data.username + "</span>" +
    "<br><br><strong >API authentication</strong><br><br>" +
    "<p>使用 PCIT API 请访问 " +
    "<a href='https://api.ci.khs1994.com' target='_blank'>https://api.ci.khs1994.com</a></p>" +
    "<input id='token' value='" + Cookies.get(git_type + '_api_token') + "'>" +
    "<button class='copy_token' data-clipboard-target='#token' onclick='copyToken()'>Copy</button><br><br>"
  );
}

function copyToken() {
  let clipboard = new ClipboardJS('.copy_token');

  clipboard.on('success', function (e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);

    e.clearSelection();
  });
}

function list(data) {
  let count = data.length;
  let repos_element = $("#repos");

  repos_element.empty().css('height', 200);

  if (count > 4) {
    repos_element.css('height', count * 50);
  }

  $.each(data, function (num, repo) {

    let button = $("<button></button>");

    let {build_status: status, repo_full_name: repo_name} = repo;

    button.attr("onclick", 'open_or_close(this)');

    if (status === (1).toString()) {
      button.text('Close');
      button.css('color', 'green');
    } else {
      button.text('Open');
      button.css('color', 'red');
    }

    button.attr("id", repo_name);
    button.css('text-align', 'right');

    if ('github' === git_type) {
      button.hide();
    }

    // <p id="username/repo">username/repo</p>
    let p = $("<a></a>").text(repo_name);

    p.attr("id", repo_name);
    p.attr('href', ci_host + git_type + '/' + repo_name);
    p.attr('target', '_blank');
    p.css('display', 'inline');

    let settings = $("<a></a>");

    settings.text('Setting');
    settings.attr('href', ci_host + git_type + "/" + repo_name + "/settings");
    settings.attr('target', '_blank');
    $("#repos").append(button).append('&nbsp;&nbsp;').append(settings).append('&nbsp;&nbsp;')
      .append(p).append('<br>');
  });
}

function showOrg(data) {
  let count = data.length;
  $.each(data, function (num, org) {
    let org_name = org.username;
    $(".orgs").append(`<p onclick="click_org(this.innerHTML)">${org_name}</p>`).css('height', count * 50);
  });
}

function showGitHubAppSettings(org_name, installation_id) {
  let settings_url;
  let content;
  $.ajax({
    type: "GET",
    url: "/api/ci/github_app_settings/" + org_name,
    success: function (data) {
      settings_url = data;
      content = `<p></p>
        <p>找不到仓库？请在
        <a href="${settings_url}/${installation_id}" target="_blank"> GitHub </a>管理你的仓库
        </p>
        `;

      $("#repos").append(content);
    }
  });
}

function showGitHubAppInstall(uid) {
  let installation_url;
  let content;

  $.ajax({
    type: "GET",
    url: "/api/ci/github_app_installation/" + uid,
    success: function (data) {

      installation_url = data;
      content = `<p>此账号或组织未安装 GitHub App 或未选择项目，点击
<a href="${installation_url}" target="_blank">激活项目</a> 在 GitHub 进行安装</p>
        `;
      $("#repos").append(content);
    },
    error: function (data) {
      console.log(data);
    }
  });
}

function click_user() {
  $.ajax({
    type: "GET",
    url: "/api/user",
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      let {installation_id, uid} = data;

      $.ajax({
        type: "GET",
        url: "/api/repos",
        headers: {
          'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
        },
        success: function (data) {
          history.pushState({}, username, ci_host + 'profile/' + git_type + '/' + username);
          history.replaceState(null, username, ci_host + 'profile/' + git_type + '/' + username);
          list(data);

          if (git_type !== 'github') {

            return 1;
          }

          if (installation_id) {
            showGitHubAppSettings(null, installation_id);
          } else {
            showGitHubAppInstall(uid);
          }
        }
      });
    }
  });
}

function show_org(data, org_name) {
  let {installation_id, uid} = data[0];

  $.ajax({
    type: "GET",
    url: "/api/repos/" + git_type + "/" + org_name,
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      history.pushState({}, org_name, ci_host + 'profile/' + git_type + '/' + org_name);
      history.replaceState(null, org_name, ci_host + 'profile/' + git_type + '/' + org_name);
      list(data);

      if (git_type !== 'github') {
        return;
      }

      if (parseInt(installation_id)) {
        showGitHubAppSettings(org_name, installation_id);
      } else {
        showGitHubAppInstall(uid);
      }
    }
  });
}

function click_org(org_name) {
  $.ajax({
    type: "GET",
    url: "/api/org/" + git_type + "/" + org_name,
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      show_org(data, org_name);
    }
  })
}

$(document).ready(function () {

  $.ajax({
    type: "GET",
    url: "/api/user",
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },

    success: function (data) {

      showUserBasicInfo(data[0]);

      if (data[0].username === username) {
        $.ajax({
          type: "GET",
          url: "/api/orgs",
          headers: {
            'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
          },

          success: function (data) {
            showOrg(data);
          }
        });
      }
    }
  });

  $.ajax({
    type: "GET",
    url: "/api/repos",
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },

    success: function (data) {
      list(data);
    }
  });

  if ('github' === git_type) {
    $.ajax({
      type: "GET",
      url: "/api/ci/oauth_client_id",
      headers: {
        'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
      },
      success: function (data) {
        $('.tip').after(`
<p><a href="${data}" target="_blank"> <button>授权</button></a></p>
`
        );
      }
    });
  }

});

function sync() {
  $.ajax({
    type: 'POST',
    url: '/api/user/sync',
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      location.reload();
      console.log(data);
    }
  });
}

function open_or_close(id) {
  let repo = id.getAttribute('id');
  let status = id.innerHTML;
  if ('Open' === status) {

    $.ajax({
      type: "POST",
      url: ci_host + "webhooks/" + git_type + "/" + repo + "/199412/activate",
      dataType: "json",
      contentType: 'application/json;charset=utf-8',
      success: function (data) {
        id.innerHTML = 'Close';
        id.style.color = 'Green';
        console.log(data);
      }
    });
  } else {

    $.ajax({
      type: "delete",
      url: ci_host + "/webhooks/" + git_type + "/" + repo + "/199412/deactivate",
      success: function (data) {
        id.innerHTML = 'Open';
        id.style.color = 'Red';
        console.log(data);
      }
    });

  }
}
