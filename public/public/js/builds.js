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

let url_array = url.split('/');

let baseUrl = "https://" + location.host;

let [, , , git_type, username, repo] = url_array;

let base_full_url = baseUrl + '/' + git_type + '/' + username + '/' + repo;

let base_include = git_type + '/' + username + '/' + repo;

let type_from_url = url_array[6];

let build_id;

let title;

if (6 === url_array.length) {
  type_from_url = 'current';
}

let repo_fullname = username + '/' + repo;

let query = git_type + '/' + repo_fullname;

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
  let build_id;

  if (8 === url_array.length) {
    build_id = url_array[7];
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

function jobs() {
  console.log('jobs');
  $.ajax({
    type: "get",
    url: '/api/repo/' + base_include + '/jobs/1',
    success: function (data) {
      display(id, data);
    }
  });
}

function settings() {
  console.log(location.href);
  $.ajax({
    type: "get",
    url: '/api/repo/' + repo_fullname + '/settings',
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + 'api_token')
    },
    success: function (data) {
      display(id, data);
    }
  });
}

function requests() {
  console.log(location.href);
  $.ajax({
    type: "get",
    url: '/api/repo/' + repo_fullname + '/requests',
    herders: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api+token')
    },
    success: function (data) {
      display(id, data);
    }
  });
}

function caches() {
  console.log(location.href);
  $.ajax({
    type: "get",
    url: '/api/repo/' + repo_fullname + '/caches',
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      display(id, data);
    }
  });
}

function triggerBuild() {
  console.log(location.href);
  $.ajax({
    type: "post",
    url: '/api/repo/' + repo_fullname + '/trigger',
    headers: {
      'Authorization': 'token ' + Cookies.get(git_type + '_api_token')
    },
    success: function (data) {
      display(id, data);
    }
  });
}

function getCommitUrl(commit_id, gittype = 'github') {
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

function getPRUrl(pull_request_id, gittype = 'github') {
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
    compare, stopped_at, jobs
  } = data;

  if (null === stopped_at) {
    stopped_at = 'This build is ' + build_status;
  } else {
    let d;
    d = new Date(parseInt(stopped_at) * 1000);
    stopped_at = d.toLocaleString();
  }

  let commit_url = getCommitUrl(commit_id);
  let div_element = $('<div class="build_data"></div>');

  div_element.append(() => {
    let build_id_element = $('<div class="build_id"></div>');
    build_id_element.append(build_id);

    return build_id_element;
  });

  div_element.append(() => {
    let branch_element = $('<div class="branch"></div>');
    branch_element.append(branch);

    let build_status_element = $('<div class="build_status"></div>');
    build_status_element.append(build_status);

    return build_status_element;
  });

  div_element.append(() => {
    let commit_url_element = $('<a></a>');
    commit_url_element.append(commit_id.slice(0, 7));
    commit_url_element.attr('title', 'View commit on GitHub');
    commit_url_element.attr('href', commit_url);
    commit_url_element.attr('target', '_blank');

    return commit_url_element;
  });

  div_element.append(() => {
    let commit_message_element = $('<div class="commit_message"></div>');
    commit_message_element.append(commit_message);

    return commit_message_element;
  });

  div_element.append(() => {
    let committer_name_element = $('<div class="committer_name"></div>');
    committer_name_element.append(committer_name);

    return committer_name_element;
  });

  div_element.append(() => {
    let compare_element = $('<a class="compare"></a>');
    compare_element.append('Compare').attr('title', 'View diff on GitHub').attr('href', compare);
    compare_element.attr('target', '_blank');

    return compare_element;
  });

  div_element.append(() => {
    let stopped_at_element = $('<div class="stopped_at"></div>');
    stopped_at_element.append(stopped_at);

    return stopped_at_element;
  });

  display_element.append(div_element);

  if (jobs.length === 1) {
    let build_log = jobs[0]['build_log'];

    if (null === build_log) {
      build_log = 'This Build is ' + build_status;
    }

    display_element.append(`<pre>${build_log}</pre>`);

    return;
  }

  $.each(jobs, function (id, data) {
    let {build_log} = data;

    if (null === build_log) {
      build_log = 'This Build is ' + build_status;
    }

    let pre_element = $('<pre></pre>');
    pre_element.append(build_log);
    pre_element.css('background', 'black').css('color', 'white');

    display_element.append(pre_element);
  });
}

function display_builds(data, display_element) {
  if (8 === url_array.length) {
    if (0 === data.length || 'error' === data) {
      display_element.append('Oops, we couldn\'t find that build!');
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

      display_element.append(`<tr>
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
    display_element.append('Not Build Yet !');
  }
}

function display_branches(data, display_element) {
  if (0 === data.length) {
    display_element.append('Not Build Yet !');
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
        display_element.append(`<tr>
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
}

function display_pullRequests(data, display_element) {
  if (0 === data.length) {
    display_element.append('No pull request builds for this repository');
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

      display_element.append(
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
}

function display_settings(data, display_element) {
  display_element.append('settings');
}

function display_requests(data, display_element) {
  display_element.append('requests');
}

function display_caches(data, display_element) {
  display_element.append('caches');
}

function display_triggerBuild(data, display_element) {
  display_element.append('triggerBuild');
}


function display(id, data) {
  let display_element = $("#display");

  display_element.empty();

  switch (id) {
    case 'current':
      if (0 === data.length) {
        display_element.append("Not Build Yet !");
      } else {
        showLog(data);
      }

      break;

    case 'builds':
      display_builds(data, display_element);

      break;
    case 'branches':
      display_branches(data, display_element);

      break;

    case "pull_requests":
      display_pullRequests(data, display_element);

      break;

    case 'settings':
      display_settings(data, display_element);
      break;

    case 'requests':
      display_requests(data, display_element);
      break;

    case 'caches':
      display_caches(data, display_element);
      break;

    case 'trigger_build':
      display_triggerBuild(data, display_element);
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

  if (id === 'more_options') {
    return;
  }

  console.log(id);

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

// https://www.cnblogs.com/yangzhi/p/3576520.html
$("#current").on({
  'click': function () {
    current();
  },
  'mouseover': function (event) {
    event.target.style.color = 'green';
    event.target.style.borderBottomStyle = 'solid';
  },
  'mouseout': function (event) {
    event.target.style.color = 'black';
    event.target.style.borderBottomStyle = 'none';
  }
});

$("#builds").on({
  'click': function () {
    builds();
  },
  'mouseover': function (event) {
    event.target.style.color = 'green';
    event.target.style.borderBottomStyle = 'solid';
  },
  'mouseout': function (event) {
    event.target.style.color = 'black';
    event.target.style.borderBottomStyle = 'none';
  }
});

$("#branches").on({
  'click': function () {
    branches();
  },
  'mouseover': function (event) {
    event.target.style.color = 'green';
    event.target.style.borderBottomStyle = 'solid';
  },
  'mouseout': function (event) {
    event.target.style.color = 'black';
    event.target.style.borderBottomStyle = 'none';
  }
});

$("#pull_requests").on({
  'click': function (event) {
    pull_requests();
    event.target.style.color = 'green';
    event.target.style.borderBottomStyle = 'solid';
  },
  'mouseover': function (event) {
    event.target.style.color = 'green';
    event.target.style.borderBottomStyle = 'solid';
  },
  'mouseout': function (event) {
    event.target.style.color = 'black';
    event.target.style.borderBottomStyle = 'none';
  }
});

$("#settings").on({
  'click': function (event) {
    settings();
  }
});

$("#caches").on({
  'click': function (event) {
    caches();
  }
});

$("#requests").on({
  'click': function (event) {
    requests();
  }
});

$("#trigger_build").on({
  'click': function (event) {
    triggerBuild();
  }
});

jQuery(document).ready(function () {

  display_title(type_from_url);

  let nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;';

  let content = jQuery('<h2></h2>');

  content.append(() => {
    return format_gittype(git_type) + nbsp + username + '/' + repo + nbsp;
  }).append(() => {
    let a_element = $('<a></a>');
    let img_element = $('<img alt="status" src=""/>');

    img_element.attr('src', base_full_url + '/status');
    a_element.append(img_element);
    a_element.attr('href', base_full_url + '/getstatus');
    a_element.attr('target', '_black');

    return a_element;
  });

  $('#repo').append(content);

  console.log(type_from_url);

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

    case 'jobs':
      jobs();
      break;

    case 'settings':
      settings();
      break;

    case 'request':
      requests();
      break;

    case 'caches':
      caches();
      break;
  }
});
