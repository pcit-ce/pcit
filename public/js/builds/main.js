const header = require('../common/header');
const footer = require('../common/footer');
const git = require('../common/git');
const title = require('./title');
const url = require('./url');

const current = require('./current');
const branches = require('./branches');
const builds_history = require('./builds_history');
const pull_requests = require('./pull_requests');
const settings = require('./settings');
const requests = require('./requests');
const caches = require('./caches');
const trigger_build = require('./triggerBuild');

const jobs = require('./jobs');

header.show();
footer.show();

const repo_full_name = url.getRepoFullName;
const git_repo_full_name = url.getGitRepoFullName;
const username = url.getUsername;
const repo = url.getRepo;
const type = url.getType;
const repo_full_name_url = url.getRepoFullNameUrl;
const git_type = url.getGitType;
const baseTitle = url.baseTitle;

const common = require('./common');
// http://www.zhangxinxu.com/wordpress/2013/06/html5-history-api-pushstate-replacestate-ajax/
// 事件冒泡 点击了 子元素 会向上传递 即也点击了父元素

$(".column").click(function (event) {
  let id = event.target.id;

  console.log('事件冒泡 ' + id);

  if (id === 'more_options') {
    return;
  }

  if (id === 'build_id') {
    // build_id 元素被点击
    common.column_click_handle(event.target.id);
  }

  title.show(baseTitle, id);
});

function changeUrl(id) {
  if ('current' === id) {
    history.pushState({}, baseTitle, repo_full_name_url);

    history.replaceState(null, baseTitle, repo_full_name_url);
  } else {
    history.pushState({}, baseTitle, repo_full_name_url + '/' + id);

    history.replaceState(null, baseTitle, repo_full_name_url + '/' + id);
  }
}

function column_el_click(id) {
  changeUrl(id);

  switch (id) {
    case 'current':
      current.handle(git_repo_full_name, username, repo);
      break;

    case 'branches':
      branches.handle(git_repo_full_name);

      break;

    case 'builds':
      builds_history.handle(git_repo_full_name, username, repo, url.getUrlWithArray);

      break;

    case 'pull_requests':
      pull_requests.handle(git_repo_full_name, username, repo, repo_full_name_url);

      break;
  }
}

function mouseoutMethod(event) {
  event.target.style.color = 'black';
  event.target.style.borderBottomStyle = 'none';
}

function mouseoverMethod(event) {
  event.target.style.color = 'green';
  event.target.style.borderBottomStyle = 'solid';
}

// https://www.cnblogs.com/yangzhi/p/3576520.html
$('.column span').on({
  'click': function (event) {

    let column_el = $('.column span');
    let target = event.target;
    let target_id = target.id;

    console.log(target_id);

    column_el_click(target_id);
    common.column_remove();
    common.column_click_handle(target.id);
  },
  'mouseover': function (event) {
    mouseoverMethod(event);
  },
  'mouseout': function (event) {
    mouseoutMethod(event);
  }
});

$("#more_options").on({
  'click': function (event) {
    console.log(url.getUrlWithArray());

    let id = event.target.id;

    if (id === 'more_options') {
      return;
    }

    // if (url.getUrlWithArray().length === 8) {
    //   return;
    // }

    let token = Cookies.get(git_type + '_api_token');

    changeUrl(id);

    common.column_remove();

    $('#pull_requests').after(() => {
      let span_el = $(`<span id="column_more_options"></span>`);

      return span_el.append((id.slice(0, 1)).toUpperCase() + id.slice(1));
    });

    common.column_click_handle('column_more_options');

    switch (id) {
      case "settings":
        settings.handle(repo_full_name, token);
        break;

      case "caches":
        caches.handle(repo_full_name, token);
        break;

      case "requests":
        requests.handle(repo_full_name, token);
        break;

      case "trigger_build":
        trigger_build.handle(repo_full_name, token);
        break;
    }
  }
});

jQuery(document).ready(function () {
  let content = jQuery('<h2></h2>');

  title.show(baseTitle, type);

  content.append(() => {
    return git.format(git_type) + repo_full_name;
  }).append(() => {
    let a_element = $('<a></a>');
    let img_element = $('<img alt="status" src=""/>');

    img_element.attr('src', repo_full_name_url + '/status');
    a_element.append(img_element);
    a_element.attr('href', repo_full_name_url + '/getstatus');
    a_element.attr('target', '_black');

    return a_element;
  });

  $('#repo').append(content);

  console.log(type);

  let token = Cookies.get(git_type + '_api_token');

  switch (type) {

    case 'current':
      current.handle(git_repo_full_name, username, repo);

      break;
    case 'branches':
      branches.handle(git_repo_full_name);

      break;

    case 'builds':
      builds_history.handle(git_repo_full_name, username, repo, url.getUrlWithArray);

      break;

    case 'pull_requests':
      pull_requests.handle(git_repo_full_name, username, repo, repo_full_name_url);

      break;

    case 'jobs':
      jobs.handle(git_repo_full_name, token);
      break;

    case 'settings':
      settings.handle(repo_full_name, token);
      break;

    case 'requests':
      requests.handle(repo_full_name, token);
      break;

    case 'caches':
      caches.handle(repo_full_name, token);
      break;
  }
});
