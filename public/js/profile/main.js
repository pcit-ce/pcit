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

  let titleContent = `${git.format(git_type)} - ${username} - Profile - ${
    app.app_name
  }`;

  title.titleChange(titleContent);

  $('#user')
    .empty()
    .append(() => $('<span></span>').append(username))
    .append(() => $('<strong></strong>').append('API authentication'))
    .append(() =>
      $('<p></p>')
        .append('使用 PCIT API 请访问')
        .append(() => {
          return $('<a></a>')
            .append('https://api.ci.khs1994.com')
            .attr({
              href: 'https://api.ci.khs1994.com',
              target: '_blank',
            });
        })
        .append(() =>
          $('<input/>').attr({
            id: 'token',
            value: token,
          }),
        )
        .append(() =>
          $('<button></button>')
            .addClass('copy_token')
            .attr({
              'data-clipboard-target': '#token',
            })
            .append('Copy'),
        ),
    );
}

$('.copy_token').on({
  click: () => {
    copyToken();
  },
});

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

// show repos
function list(data) {
  let repos_element = $('#repos');

  repos_element.empty();

  $.each(data, function(num, repo) {
    let repo_item_el = $('<div class="repo_item row"></div>');
    let { build_status: status, repo_full_name: repo_name } = repo;
    let button = $('<button></button>');

    button.addClass('open_or_close btn btn-light').attr('repo_name', repo_name);

    if (status === 1 + '') {
      button.text('Close');
      button.css('color', 'green');
    } else {
      button.text('Open');
      button.css('color', 'red');
    }

    // <p id="username/repo">username/repo</p>
    let p = $('<a class="repo_full_name col-12 col-md-9"></a>')
      .text(repo_name)
      .attr({
        repo_name: repo_name,
        href: ci_host + git_type + '/' + repo_name,
        target: '_blank',
      })
      .css('display', 'inline');

    let settings = $(
      '<a class="settings material-icons col-12 col-md-3">settings</a>',
    )
      .attr('href', ci_host + [git_type, repo_name, 'settings'].join('/'))
      .attr('target', '_blank');

    repo_item_el
      .append(() => {
        return 'github' === git_type ? button.hide() : button;
      })
      .append(p)
      .append(settings);

    repos_element.append(repo_item_el);
  });
}

// show org list
function showOrg(data) {
  let count = data.length;

  $.each(data, (num, org) => {
    let { username: org_name } = org;

    $('.orgs')
      .append(() => {
        return $('<p class="org_name"></p>')
          .append(org_name)
          .attr({
            org_name: org_name,
          });
      })
      .css('height', count * 50);
  });
}

function showGitHubAppSettings(org_name, installation_id) {
  (async () => {
    let settings_url = await new Promise(resolve => {
      $.ajax({
        type: 'GET',
        url: '/api/ci/github_app_settings/' + org_name,
        success(data) {
          resolve(data);
        },
      });
    });

    $('#repos').append(() => {
      return $('<p class="repo_tips"></p>')
        .append('找不到仓库？请在 ')
        .append(() => {
          return $('<a></a>')
            .attr({
              href: `${settings_url}/${installation_id}`,
              target: '_blank',
            })
            .text('GitHub');
        })
        .append(' 添加仓库');
    });
  })();
}

function showGitHubAppInstall(uid) {
  (async () => {
    let installation_url = await new Promise(resolve => {
      $.ajax({
        type: 'GET',
        url: '/api/ci/github_app_installation/' + uid,
        success: function(data) {
          resolve(data);
        },
      });
    });

    $('#repos').append(
      $('<p class="repo_tips"></p>')
        .append(() => '此账号或组织未安装 GitHub App 或未选择项目，点击 ')
        .append(() => {
          return $('<a></a>')
            .attr({
              href: installation_url,
              target: '_blank',
            })
            .text('激活项目');
        })
        .append(' 在 GitHub 进行安装'),
    );
  })();
}

function get_userdata() {
  return new Promise(resolve => {
    $.ajax({
      type: 'GET',
      url: '/api/user',
      headers: {
        Authorization: 'token ' + token,
      },
      success: function(data) {
        resolve(data);
      },
    });
  });
}

function click_user() {
  (async () => {
    let data = await get_userdata();

    let { installation_id, uid, username, name, pic } = data[0];

    let repo_data = await new Promise(resolve => {
      $.ajax({
        type: 'GET',
        url: '/api/repos',
        headers: {
          Authorization: 'token ' + token,
        },
        success: function(data) {
          resolve(data);
        },
      });
    });

    history.pushState(
      {},
      username,
      ci_host + 'profile/' + git_type + '/' + username,
    );

    $('.header_img').attr('src', pic);
    $('.details_usernickname').text(name ? name : username);
    $('.details_username').text('@' + username);

    list(repo_data);

    if (git_type !== 'github') {
      return;
    }

    parseInt(installation_id)
      ? showGitHubAppSettings(null, installation_id)
      : showGitHubAppInstall(uid);
  })();
}

function show_org(data, org_name) {
  if (data[0] === undefined) {
    return;
  }

  let { pic, username, name } = data[0];

  $('.header_img').attr('src', pic);
  $('.details_usernickname').text(name ? name : username);
  $('.details_username').text('@' + username);

  let { installation_id, uid } = data[0];

  (async () => {
    let org_data = await new Promise(resolve => {
      $.ajax({
        type: 'GET',
        url: '/api/repos/' + git_type + '/' + org_name,
        headers: {
          Authorization: 'token ' + token,
        },
        success: function(data) {
          resolve(data);
        },
      });
    });

    history.pushState(
      {},
      org_name,
      ci_host + 'profile/' + git_type + '/' + org_name,
    );

    list(org_data);

    if (git_type !== 'github') {
      return;
    }

    parseInt(installation_id)
      ? showGitHubAppSettings(org_name, installation_id)
      : showGitHubAppInstall(uid);
  })();
}

function click_org(org_name) {
  $.ajax({
    type: 'GET',
    url: '/api/org/' + git_type + '/' + org_name,
    headers: {
      Authorization: 'token ' + token,
    },
    success: function(data) {
      show_org(data, org_name);
    },
  });
}

function get_user_repos() {
  return new Promise(resolve => {
    $.ajax({
      type: 'GET',
      url: '/api/repos',
      headers: {
        Authorization: 'token ' + token,
      },
      success: function(data) {
        resolve(data);
      },
    });
  });
}

$(document).ready(function() {
  (async () => {
    let data = await get_userdata();

    showUserBasicInfo(data[0]);

    let { installation_id, username: api_username, uid } = data[0];

    if (api_username === username) {
      $.ajax({
        type: 'GET',
        url: '/api/orgs',
        headers: {
          Authorization: 'token ' + token,
        },

        success: function(data) {
          showOrg(data);
        },
      });
    }

    let user_repo_data = await get_user_repos();

    list(user_repo_data);

    if ('github' === git_type) {
      parseInt(installation_id)
        ? showGitHubAppSettings(username, installation_id)
        : showGitHubAppInstall(uid);
      return;
    }

    let oauth_client_url = await new Promise(resolve => {
      $.ajax({
        type: 'GET',
        url: '/api/ci/oauth_client_id',
        headers: {
          Authorization: 'token ' + token,
        },
        success: function(data) {
          resolve(data);
        },
      });
    });

    $('.tip').after(() => {
      $('<p></p>').append(() => {
        return $('<a></a>')
          .append('<button>授权</button>')
          .attr({
            href: oauth_client_url,
            target: '_blank',
          });
      });
    });
  })();
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
            'aria-valuemax': 100,
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
      Authorization: 'token ' + token,
    },
    success: function(data) {
      location.reload();
      console.log(data);
    },
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
      },
    });
  } else {
    $.ajax({
      type: 'delete',
      url: ci_host + 'webhooks/' + git_type + '/' + repo + '/deactivate',
      success: function(data) {
        target.innerHTML = 'Open';
        target.style.color = 'Red';
        console.log(data);
      },
    });
  }
}

$(document).on('click', '.org_name', function() {
  click_org($(this).attr('org_name'));
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
