'use strict';

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

// const jobs = require('./jobs');

const repo_full_name = url.getRepoFullName;
const git_repo_full_name = url.getGitRepoFullName;
const username = url.getUsername;
const repo = url.getRepo;
let type = url.getType;
const repo_full_name_url = url.getRepoFullNameUrl;
const git_type = url.getGitType;
const baseTitle = url.baseTitle;

const common = require('./common');

header.show();
footer.show();

// 事件冒泡 点击了 子元素 会向上传递 即也点击了父元素

$('.column').click(function (event) {
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

// http://www.zhangxinxu.com/wordpress/2013/06/html5-history-api-pushstate-replacestate-ajax/
// https://developer.mozilla.org/zh-CN/docs/Web/API/History_API

function changeUrl(id, replace = false) {
  if ('current' === id) {
    if (replace) {
      history.replaceState({'key_id': id}, baseTitle, repo_full_name_url);
      return;
    }
    history.pushState({'key_id': id}, baseTitle, repo_full_name_url);

  } else {
    if (replace) {
      if (8 === url.getUrlWithArray().length) {
        history.replaceState({'key_id': id}, null, null);
        return;
      }
      history.replaceState({'key_id': id}, baseTitle, repo_full_name_url + '/' + id);
      return;
    }
    history.pushState({'key_id': id}, baseTitle, repo_full_name_url + '/' + id);
  }
}

function getToken() {
// eslint-disable-next-line no-undef
  return Cookies.get(git_type + '_api_token');
}

function column_el_click(id, change_url = true) {

  change_url && changeUrl(id);

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

    case 'settings':
      settings.handle(repo_full_name, getToken());
      break;

    case 'caches':
      caches.handle(repo_full_name, getToken());
      break;

    case 'requests':
      requests.handle(repo_full_name, getToken());
      break;

    case 'trigger_build':
      trigger_build.handle(repo_full_name, getToken());
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

    let target = event.target;
    let id = target.id;

    console.log(id);

    column_el_click(id);
    common.column_remove();
    common.column_click_handle(id);
  },
  'mouseover': function (event) {
    mouseoverMethod(event);
  },
  'mouseout': function (event) {
    mouseoutMethod(event);
  }
});

$('#more_options').on({
  'click': function (event) {
    console.log(url.getUrlWithArray());

    let id = event.target.id;

    if (id === 'more_options') {
      return;
    }

    // if (url.getUrlWithArray().length === 8) {
    //   return;
    // }

    column_el_click(id);  // 渲染 display 页面
    common.column_remove(); // 移除其他元素
    common.column_click_handle(id); // 增加元素
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

  common.column_remove(); // 移除 column
  column_el_click(type, false); // 渲染 display 页面
  if (url.getUrlWithArray().length === 8) {
    type = 'build_id';
  }
  common.column_click_handle(type); // 渲染被点击的 column
  changeUrl(type, true);
});

window.onpopstate = (event) => {
  let id = event.state.key_id;
  console.log(id);

  column_el_click(id, false); // 渲染 display 页面
  common.column_remove(); // 移除 column
  common.column_click_handle(id); // 渲染被点击的 column
};
