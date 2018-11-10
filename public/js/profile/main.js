'use strict';

const header = require('../common/header');
const footer = require('../common/footer');
const git = require('../common/git');
const app = require('../common/app');
const title = require('../common/title');

header.show();
footer.show();

let ci_host = 'https://' + location.host + '/';
let url_array = location.href.split('/');
let git_type = url_array[4];
let username = url_array[5];

// eslint-disable-next-line no-undef
let token = Cookies.get(git_type + '_api_token');

function showUserBasicInfo(data) {
  let { username, type } = data;

  $('#username')
    .text(username)
    .addClass(type);

  let titleContent = `${git.format(git_type)} - ${data.username} - Profile - ${
    app.app_name
  }`;

  title.titleChange(titleContent);

  $('#user')
    .empty()
    .append(
      '<span>' +
        username +
        '</span>' +
        '<br><br><strong >API authentication</strong><br><br>' +
        '<p>使用 PCIT API 请访问 ' +
        "<a href='https://api.ci.khs1994.com' target='_blank'>https://api.ci.khs1994.com</a></p>" +
        "<input id='token' value='" +
        token +
        "'>" +
        "<button class='copy_token' data-clipboard-target='#token'>Copy</button><br><br>"
    );
}

(() => {
  $('.copy_token').on({
    click: () => {
      copyToken();
    }
  });
})();

function copyToken() {
  // eslint-disable-next-line no-undef
  let clipboard = new ClipboardJS('.copy_token');

  clipboard.on('success', function(e) {
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);

    e.clearSelection();
  });
}

function list(data) {
  let count = data.length;
  let repos_element = $('#repos');
  let orgs_element = $('#orgs');

  repos_element.empty().css('height', 200);
  orgs_element.css('height', 220);

  if (count > 3) {
    let css_height = count * 50;
    repos_element.css('height', css_height);
    orgs_element.css('height', css_height);
  }

  $.each(data, function(num, repo) {
    let repo_item_el = $('<div class="repo_item"></div>');

    let { build_status: status, repo_full_name: repo_name } = repo;

    let button = $('<button></button>');

    button.addClass('open_or_close btn btn-light').attr('repo', repo_name);

    if (status === 1 + '') {
      button.text('Close');
      button.css('color', 'green');
    } else {
      button.text('Open');
      button.css('color', 'red');
    }

    button.attr('id', repo_name);
    button.css('text-align', 'right');

    // <p id="username/repo">username/repo</p>
    let p = $('<a class="repo_full_name"></a>');

    p.text(repo_name);
    p.attr('id', repo_name);
    p.attr('href', ci_host + git_type + '/' + repo_name);
    p.attr('target', '_blank');
    p.css('display', 'inline');

    let settings = $('<a class="settings material-icons">settings</a>')
      .attr('href', ci_host + [git_type, repo_name + 'settings'].join('/'))
      .attr('target', '_blank');

    repo_item_el
      .append(() => {
        return 'github' === git_type ? button.hide() : button;
      })
      .append(settings)
      .append(p);

    repos_element.append(repo_item_el);
  });
}

function showOrg(data) {
  let count = data.length;

  $.each(data, function(num, org) {
    let { username: org_name } = org;
    let orgs_element = $('.orgs');

    orgs_element
      .append(`<p class = "org_name">${org_name}</p>`)
      .css('height', count * 50);
  });
}

function showGitHubAppSettings(org_name, installation_id) {
  let settings_url;
  let content;
  let repos_element = $('#repos');

  $.ajax({
    type: 'GET',
    url: '/api/ci/github_app_settings/' + org_name,
    success: function(data) {
      settings_url = data;

      content = $('<p class="repo_tips"></p>');

      content
        .append('找不到仓库？请在 ')
        .append(() => {
          let a_element = $('<a></a>');
          a_element.attr('href', `${settings_url}/${installation_id}`);
          a_element.attr('target', '_blank');
          a_element.text('GitHub');

          return a_element;
        })
        .append(' 添加仓库');

      repos_element.append('<p></p>').append(content);
    }
  });
}

function showGitHubAppInstall(uid) {
  let installation_url;
  let content;

  $.ajax({
    type: 'GET',
    url: '/api/ci/github_app_installation/' + uid,
    success: function(data) {
      let repos_element = $('#repos');

      installation_url = data;
      content = $('<p class="repo_tips"></p>');
      content.append(() => '此账号或组织未安装 GitHub App 或未选择项目，点击 ');
      content.append(() => {
        let a_element = $('<a></a>');
        a_element.attr('href', installation_url);
        a_element.attr('target', '_blank');
        a_element.text('激活项目');

        return a_element;
      });

      content.append(' 在 GitHub 进行安装');
      repos_element.append(content);
    },
    error: function(data) {
      console.log(data);
    }
  });
}

function click_user() {
  $.ajax({
    type: 'GET',
    url: '/api/user',
    headers: {
      Authorization: 'token ' + token
    },
    success: function(data) {
      let { installation_id, uid } = data[0];

      $.ajax({
        type: 'GET',
        url: '/api/repos',
        headers: {
          Authorization: 'token ' + token
        },
        success: function(data) {
          history.pushState(
            {},
            username,
            ci_host + 'profile/' + git_type + '/' + username
          );
          history.replaceState(
            null,
            username,
            ci_host + 'profile/' + git_type + '/' + username
          );
          list(data);

          if (git_type !== 'github') {
            return;
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
  if (data[0] === undefined) {
    return;
  }

  let { installation_id, uid } = data[0];

  $.ajax({
    type: 'GET',
    url: '/api/repos/' + git_type + '/' + org_name,
    headers: {
      Authorization: 'token ' + token
    },
    success: function(data) {
      history.pushState(
        {},
        org_name,
        ci_host + 'profile/' + git_type + '/' + org_name
      );
      history.replaceState(
        null,
        org_name,
        ci_host + 'profile/' + git_type + '/' + org_name
      );
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
    type: 'GET',
    url: '/api/org/' + git_type + '/' + org_name,
    headers: {
      Authorization: 'token ' + token
    },
    success: function(data) {
      show_org(data, org_name);
    }
  });
}

$(document).ready(function() {
  $.ajax({
    type: 'GET',
    url: '/api/user',
    headers: {
      Authorization: 'token ' + token
    },

    success(data) {
      showUserBasicInfo(data[0]);

      if (data[0].username === username) {
        $.ajax({
          type: 'GET',
          url: '/api/orgs',
          headers: {
            Authorization: 'token ' + token
          },

          success: function(data) {
            showOrg(data);
          }
        });
      }
    }
  });

  $.ajax({
    type: 'GET',
    url: '/api/repos',
    headers: {
      Authorization: 'token ' + token
    },

    success: function(data) {
      list(data);
    }
  });

  if ('github' === git_type) {
    $.ajax({
      type: 'GET',
      url: '/api/ci/oauth_client_id',
      headers: {
        Authorization: 'token ' + token
      },
      success: function(data) {
        $('.tip').after(`
<p><a href="${data}" target="_blank"> <button>授权</button></a></p>
`);
      }
    });
  }
});

$('#sync').on('click', function() {
  $(this)
    .empty()
    .append('账户信息同步中')
    .attr('disabled', 'disabled');

  $(this).after(() => {
    return $('<div></div>')
      .addClass('progress')
      .append(() => {
        return $('<div></div>')
          .addClass('progress-bar progress-bar-striped progress-bar-animated')
          .attr({
            role: 'progressbar',
            'aria-valuenow': 10,
            'aria-valuemin': 0,
            'aria-valuemax': 100
          })
          .css('width', '20%');
      });
  });

  function progress(progress, timeout) {
    setTimeout(() => {
      $('.progress-bar')
        .attr('aria-valuenow', progress)
        .css('width', progress + '%');
    }, timeout);
  }

  $.ajax({
    type: 'POST',
    url: '/api/user/sync',
    headers: {
      Authorization: 'token ' + token
    },
    success: function(data) {
      location.reload();
      console.log(data);
    }
  });

  progress(20, 2000);
  progress(40, 10000);
  progress(80, 15000);
  progress(97, 30000);
});

function open_or_close(target) {
  let status = target.innerHTML;
  let repo = target.id;

  if ('Open' === status) {
    $.ajax({
      type: 'POST',
      url: ci_host + 'webhooks/' + git_type + '/' + repo + '/activate',
      dataType: 'json',
      contentType: 'application/json;charset=utf-8',
      success: function(data) {
        target.innerHTML = 'Close';
        target.style.color = 'Green';
        console.log(data);
      }
    });
  } else {
    $.ajax({
      type: 'delete',
      url: ci_host + 'webhooks/' + git_type + '/' + repo + '/deactivate',
      success: function(data) {
        target.innerHTML = 'Open';
        target.style.color = 'Red';
        console.log(data);
      }
    });
  }
}

$('#orgs').click(function(event) {
  let org_name = event.target.innerHTML;
  click_org(org_name);
});

$('#userinfo').click(function(event) {
  let username = event.target.innerHTML;
  click_user(username);
});

// append 添加元素绑定事件
// https://www.cnblogs.com/liubaojing/p/8383960.html

$('#repos').on('click', '.open_or_close', function(event) {
  console.log(event);

  open_or_close(event.target);
});
