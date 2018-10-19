$('header').append(`<span class="ico"><img alt='pcit' title="PCIT IS A PHP CI TOOLKIT" id="pcit_ico" src="/ico/pcit.png"/></span>
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

let url = location.href;

let array = url.split('/');

let baseUrl = "https://" + location.host;

let [, , , git_type, username, repo] = array;

let base_full_url = baseUrl + '/' + git_type + '/' + username + '/' + repo;

let base_include = git_type + '/' + username + '/' + repo;

let type_from_url = array[6];

let build_id;

let title;

if (6 === array.length) {
  type_from_url = 'current';
}

let query = git_type + '/' + username + '/' + repo;

let baseTitle = format_gittype(git_type) + ' - ' + username + '/' + repo + ' - PCIT';

function format_gittype(gittype) {
  switch (gittype) {
    case 'github':
      return 'GitHub';

    default:
      return gittype.substring(0, 1).toUpperCase() + gittype.substring(1);
  }
}

function current() {
  $.ajax({
    type: "GET",
    url: '/api/repo/' + base_include + '/build/current',
    success: function (data) {
      display('current', data);
    }
  });
}

function branches() {
  $.ajax({
    type: "GET",
    url: '/api/repo/' + base_include + '/branches',
    success: function (data) {
      display('branches', data)
    }
  });
}

function builds() {
  let url = location.href;

  let array = url.split('/');

  let build_id;

  if (8 === array.length) {
    build_id = array[7];
  }

  if (build_id) {
    $.ajax({
      type: 'GET',
      url: '/api/build/' + build_id,
      success: function (data) {
        display('builds', data);
      },
      error: function (data) {
        display('builds', 'error');
        console.log(data);
      }
    });

    return;
  }

  $.ajax({
    type: 'GET',
    url: '/api/repo/' + base_include + '/builds',
    success: function (data) {
      display('builds', data);
    },
    error: function (data) {
      display('builds', 'error');
      console.log(data);
    }
  });
}

function pull_requests() {
  $.ajax({
    type: 'GET',
    url: '/api/repo/' + base_include + '/builds?type=pr',
    success: function (data) {
      display('pull_requests', data)
    }
  })
}

function getCommitUrl(commit_id) {
  let commitUrl;

  switch (git_type) {

    case 'github':
      commitUrl = 'https://github.com/' + username + '/' + repo + '/commit/' + commit_id;
      break;

    case 'gitee':
      commitUrl = `https://gitee.com/${username}/${repo}/commit/${commit_id}`;

      break;
  }

  return commitUrl;
}

function getPRUrl(pull_request_id) {
  let prUrl;

  switch (git_type) {
    case 'github':
      prUrl = `https://github.com/${username}/${repo}/pull/${pull_request_id}`;

      break;

    case 'gitee':
      prUrl = `https://gitee.com/${username}/${repo}/pulls/${pull_request_id}`;

      break;
  }

  return prUrl;
}

function showLog(data) {
  let nbsp = '&nbsp;&nbsp;';
  console.log(data);
  let display_element = $("#display");
  let {
    id: build_id, build_status, commit_id, commit_message, branch, committer_name,
    compare, stopped_at, build_log, jobs
  } = data;

  if (null === build_log) {
    build_log = 'This Build is ' + build_status;
  }
  if (null === stopped_at) {
    stopped_at = 'This build is ' + build_status;
  } else {
    let d;
    d = new Date(parseInt(stopped_at) * 1000);
    stopped_at = d.toLocaleString();
  }

  let commit_url = getCommitUrl(commit_id);

  display_element.append(`<h2># ${build_id}${nbsp}${branch}${nbsp}${build_status}${nbsp}
<a title="View commit on GitHub" href="${commit_url}" target='_blank'>${commit_id.slice(0, 7)}</a>${nbsp}
${commit_message}${nbsp}${committer_name}${nbsp}
<a title="View diff on GitHub" href="${compare}" target='_blank'>Compare </a>${nbsp}${stopped_at}${nbsp}</h2>`
  );

  if (jobs.length === 1) {
    $("#display").append(`<pre>${jobs[0]['build_log']}</pre>`);

    return;
  }

  $.each(jobs, function (id, data) {
    $("#display").append(`<pre style="background: black; color: white">${data.build_log}</pre>`);
  });
}

function display(id, data) {
  let display_element = $("#display");

  display_element.empty();

  switch (id) {
    case 'current':
      if (0 === data.length) {
        $("#display").append("Not Build Yet !");
      } else {
        showLog(data);
      }

      break;

    case 'builds':
      let url = location.href;

      let array = url.split('/');
      if (8 === array.length) {
        if (0 === data.length || 'error' === data) {
          $("#display").append('Oops, we couldn\'t find that build!');
        } else {
          showLog(data);
        }

      } else if (0 !== data.length) {
        let i = data.length + 1;
        $.each(data, function (id, status) {
          i--;

          let {
            event_type, id: build_id, branch, committer_username,
            commit_message, commit_id, build_status, started_at, finished_at: stopped_at
          } = status;

          let nbsp = "&nbsp;&nbsp;&nbsp;";
          let nbsp2 = nbsp + nbsp + nbsp;

          let commit_url = getCommitUrl(commit_id);
          commit_id = commit_id.substr(0, 7);

          if (null == started_at) {
            started_at = nbsp2 + nbsp2 + 'Pending'
          } else {
            let d;
            d = new Date(parseInt(started_at) * 1000);
            started_at = d.toLocaleString();
          }

          if (null == stopped_at) {
            stopped_at = ' '
          } else {
            let d;
            d = new Date(parseInt(stopped_at) * 1000);
            stopped_at = d.toLocaleString();
          }

          $("#display").append(`<tr>
<td>${i}${nbsp}</td>
<td style='color:blue;'>${nbsp}${event_type}${nbsp}</td>
<td title="${branch}">${nbsp}${branch.slice(0, 10)}${nbsp}</td>
<td title="${committer_username}">${committer_username.slice(0, 10)}${nbsp}</td>
<td style='color: brown' title="${commit_message}">${nbsp}${commit_message.slice(0, 28)}${nbsp}</td>
<td>${nbsp}<a class="details" title="View commit on GitHub" href="${commit_url}" target='_blank'>${commit_id}</a>${nbsp}</td>
<td><a class="details" href="${location.href}/${build_id}" target='_blank'># ${build_id}&nbsp;${build_status}</a>${nbsp}</td>
<td>${nbsp}${started_at}${nbsp}</td>
<td>${nbsp}${stopped_at}${nbsp}</td>
</tr>
`
          )
        });
      } else {
        $("#display").append('Not Build Yet !');
      }

      break;
    case 'branches':
      if (0 === data.length) {
        $("#display").append('Not Build Yet !')
      } else {

        $.each(data, function (num, branch) {

          display_element.append(branch);

          $.each(status, function (id, status) {
            id = id.replace('k', '');

            let nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

            let stopped_at = status[3];

            if (null == stopped_at) {
              stopped_at = '&nbsp;&nbsp;&nbsp;Pending &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            } else {
              let d;
              d = new Date(stopped_at * 1000);
              stopped_at = d.toLocaleString();
            }
            $("#display").append(`<tr>
<td><a href="${builds}/${id}" target='_blank'># ${id} </a></td>
<td>${nbsp}${status[0]}${nbsp}</td>
<td>${nbsp}${status[2]}${nbsp}</td>
<td>${nbsp}${stopped_at}${nbsp}</td>
<td><a href="${status[4]}" target='_black'>${status[1]}</a></td>
</tr>
`);
          });

          display_element.append("<hr>");

        })
      }

      break;

    case "pull_requests":
      if (0 === data.length) {
        $("#display").append('No pull request builds for this repository');
      } else {
        let i = data.length + 1;
        $.each(data, function (id, status) {
          i--;

          let event_type = 'PR # ';
          let nbsp = "&nbsp;&nbsp;&nbsp;";

          let {
            pull_request_number: pull_request_id, id: build_id, branch, committer_username,
            commit_message, commit_id, build_status, started_at, finished_at: stopped_at,
          } = status;

          let commit_url = getCommitUrl(commit_id);

          let pull_request_url = getPRUrl(pull_request_id);

          commit_id = commit_id.substr(0, 7);

          if (null == started_at) {
            started_at = 'Pending'
          } else {
            let d;
            d = new Date(started_at * 1000);
            started_at = d.toLocaleString();
          }

          if (null == stopped_at) {
            stopped_at = ' ';
          } else {
            let d;
            d = new Date(stopped_at * 1000);
            stopped_at = d.toLocaleString();
          }

          $("#display").append(
            `
<tr>
<td>${i}${nbsp}</td>
<td><a title="View Pull Request on GitHub" href="${pull_request_url}" target="_blank">${event_type}${pull_request_id}</a>${nbsp}</td>
<td>${nbsp}${branch}${nbsp}</td>
<td>${committer_username}${nbsp}</td>
<td style='color:brown'> ${nbsp}${commit_message}${nbsp}</td>
<td>${nbsp}<a class="details" title="View commit on GitHub" href="${commit_url}" target='_blank'>${commit_id}</a>${nbsp}</td>
<td><a class="details" href="${base_full_url}/builds/${build_id}" target='_blank'> #${build_id}${nbsp}${build_status}</a>${nbsp}</td>
<td>${nbsp}${started_at}${nbsp}</td>
<td>${nbsp}${stopped_at}${nbsp}</td>
</tr>
`
          )
        });
      }

      break;
  }
}

function display_title(id) {

  switch (id) {
    case "pull_requests":
      title = 'Pull Requests - ' + baseTitle;
      break;
    case "builds":
      title = 'Builds - ' + baseTitle;
      break;
    default:
      title = baseTitle;
  }

  $("title").text(title);
}

// http://www.zhangxinxu.com/wordpress/2013/06/html5-history-api-pushstate-replacestate-ajax/
// 事件冒泡 点击了 子元素 会向上传递 即也点击了父元素

$(".column").click(function (event) {
  let id = event.target.id;

  if ('current' === id) {
    history.pushState({}, baseTitle, baseUrl + '/' + query);

    history.replaceState(null, baseTitle, baseUrl + '/' + query);
  } else {
    history.pushState({}, baseTitle, baseUrl + '/' + query + '/' + id);

    history.replaceState(null, baseTitle, baseUrl + '/' + query + '/' + id);
  }

  display_title(id);

  $("title").text(title);
});

$("option").click(function () {
  let id = $(this).attr('value');
  history.pushState({}, baseTitle, baseUrl + '/' + query + '/' + id);

  history.replaceState(null, baseTitle, baseUrl + '/' + query + '/' + id);

  $('title').text(baseTitle);

  $.ajax({
    type: "post",
    url: location.href,
    success: function (data) {
      display(id, data);
    }
  });
});

// https://www.cnblogs.com/yangzhi/p/3576520.html
$("#current").on({
  'click': function () {
    current();
  }
});

$("#builds").on({
  'click': function () {
    builds();
  }
});

$("#branches").on({
  'click': function () {
    branches();
  }
});

$("#pull_requests").on({
  'click':function(){
   pull_requests();
  }
});

$(document).ready(function () {

  display_title(type_from_url);

  let nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';

  $('#repo').append('<h2>' + format_gittype(git_type) + nbsp + username + '/' + repo + nbsp +
    "<a href='" + base_full_url + "/getstatus' target='_blank'>" +
    '<img alt="status" src="' + base_full_url + '/status" />' + '</a></h2>'
  );

  switch (type_from_url) {
    case 'current':
      current();

      break;
    case 'branches':
      branches();

      break;

    case 'builds':
      builds();

      break;

    case 'pull_requests':
      pull_requests();

      break;
  }
});
