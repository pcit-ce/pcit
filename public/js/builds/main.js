const header = require('../common/header');
const footer = require('../common/footer');
const git = require('../common/git');
const title = require('./title');
const {
  url,
  url_array,
  baseUrl,
  git_type,
  username,
  repo,
  repo_full_name,
  git_repo_full_name,
  repo_full_name_url,
  type_from_url,
  baseTitle,
} = require('./data');

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

// http://www.zhangxinxu.com/wordpress/2013/06/html5-history-api-pushstate-replacestate-ajax/
// 事件冒泡 点击了 子元素 会向上传递 即也点击了父元素

$(".column").click(function (event) {
  let id = event.target.id;

  if (id === 'more_options') {
    return;
  }

  console.log(id);

  if ('current' === id) {
    history.pushState({}, baseTitle, baseUrl + '/' + git_repo_full_name);

    history.replaceState(null, baseTitle, baseUrl + '/' + git_repo_full_name);
  } else {
    history.pushState({}, baseTitle, baseUrl + '/' + git_repo_full_name + '/' + id);

    history.replaceState(null, baseTitle, baseUrl + '/' + git_repo_full_name + '/' + id);
  }

  title.show(baseTitle, id);
});

function column_el_click(id) {
  switch (id) {
    case 'current':
      current.handle(git_repo_full_name, username, repo);
      break;

    case 'branches':
      branches.handle(git_repo_full_name);

      break;

    case 'builds':
      builds_history.handle(git_repo_full_name, username, repo, url_array);

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

let column_el = $('.column span');

// https://www.cnblogs.com/yangzhi/p/3576520.html
$(column_el).on({
  'click': function (event) {

    let target = event.target;
    let target_id = target.id;

    column_el_click(target_id);

    // 移除其他元素的颜色
    column_el.css('color', '#000000').css('border-bottom-style', 'none');
    // 启用其他元素的鼠标移出事件
    column_el.on({
      'mouseout': (event) => {
        mouseoutMethod(event);
      }
    });

    // 关闭该元素的鼠标移出事件
    $('#' + target_id).off('mouseout');

    // 最后对被点击元素
    target.style.color = 'green';
    target.style.borderBottomStyle = 'solid';

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
    let id = event.target.id;
    let token = Cookies.get(git_type + '_api_token');

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

  title.show(baseTitle, type_from_url);

  let content = jQuery('<h2></h2>');

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

  console.log(type_from_url);

  let token = Cookies.get(git_type + '_api_token');

  switch (type_from_url) {

    case 'current':
      current.handle(git_repo_full_name, username, repo);

      break;
    case 'branches':
      branches.handle(git_repo_full_name);

      break;

    case 'builds':
      builds_history.handle(git_repo_full_name, username, repo, url_array);

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

    case 'request':
      requests.handle(repo_full_name, token);
      break;

    case 'caches':
      caches.handle(repo_full_name, token);
      break;
  }
});
