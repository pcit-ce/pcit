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
const jobs = require('./jobs');
const repo = require('./repo');

const common = require('./common');
const common_status = require('../common/status');
const token = require('../common/token');

header.show();
footer.show();
const on_event = require('./on');

if (!token.getToken(url.getGitType())) {
  $('.more_options .auth').hide();
}

// http://www.zhangxinxu.com/wordpress/2013/06/html5-history-api-pushstate-replacestate-ajax/
// https://developer.mozilla.org/zh-CN/docs/Web/API/History_API
// 标题参数目前无效
function changeUrl(id, replace = false) {
  if ('current' === id) {
    if (replace) {
      history.replaceState({ key_id: id }, null, url.getRepoFullNameUrl());
      return;
    }

    history.pushState({ key_id: id }, null, url.getRepoFullNameUrl());
  } else {
    if (replace) {
      if (8 === url.getUrlWithArray().length) {
        history.replaceState({ key_id: id }, null, null);
        return;
      }
      history.replaceState(
        { key_id: id },
        null,
        url.getRepoFullNameUrl() + '/' + id,
      );
      return;
    }

    history.pushState(
      { key_id: id },
      null,
      url.getRepoFullNameUrl() + '/' + id,
    );
  }
}

function column_el_click(id, change_url = true) {
  change_url && changeUrl(id);

  switch (id) {
    case 'current':
      current.handle(url);
      break;

    case 'branches':
      branches.handle(url);

      break;

    case 'builds':
      builds_history.handle(url);

      break;

    case 'pull_requests':
      pull_requests.handle(url);

      break;

    case 'settings':
      settings.handle(url, token);
      break;

    case 'caches':
      caches.handle(url, token);
      break;

    case 'requests':
      requests.handle(url, token);
      break;

    case 'trigger_build':
      trigger_build.handle(url, token);
      break;

    case 'jobs':
      jobs.handle(url);
      break;

    case 'repo':
      repo.handle(
        url.getGitType(),
        url.getUsername(),
        token.getToken(url.getGitType()),
      );
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
  click: function(event) {
    let target = event.target;
    let id = target.id;

    console.log(id);

    column_el_click(id);
    common.column_remove();
    common.column_click_handle(id);
  },
  mouseover: function(event) {
    mouseoverMethod(event);
  },
  mouseout: function(event) {
    mouseoutMethod(event);
  },
});

$('.more_options').on({
  click: function(event) {
    console.log(url.getUrlWithArray());

    let id = event.target.id;

    if (id === 'more_options') {
      return;
    }

    // if (url.getUrlWithArray().length === 8) {
    //   return;
    // }

    column_el_click(id); // 渲染 display 页面
    common.column_remove(); // 移除其他元素
    common.column_click_handle(id); // 增加元素
  },
});

// 处理页面加载，用户首次进入
$(() => {});

// =

$(document).ready(() => {});

// =

jQuery(document).ready(function() {
  let content = jQuery('<h1 class="repo_title"></h1>');

  let type = url.getType();

  title.show(url.getBaseTitle(), type);

  content
    .append(() => {
      return $('<a class="h1_git_type"></a>')
        .append(() => {
          return $('<div></div>')
            .append(git.format(url.getGitType()))
            .css('float', 'left');
        })
        .attr(
          'href',
          [
            git.getUrl(url.getUsername(), url.getRepo(), url.getGitType()),
            url.getUsername(),
            url.getRepo(),
          ].join('/'),
        )
        .css({
          display: 'block',
        })
        .attr('target', '_block')
        .append(
          $('<span></span>')
            .addClass('badge badge-dark badge-pill')
            .append('Beta')
            .css({
              'font-size': '11px',
              display: 'block',
              float: 'left',
              'margin-right': '10px',
            }),
        );
    })
    .append(() => {
      let span_el = $('<a class="h1_username">');
      span_el
        .append(url.getUsername())
        .attr(
          'href',
          [url.getHost(), url.getGitType(), url.getUsername()].join('/'),
        );

      return span_el;
    })
    .append(() => {
      let span_el = $('<span></span>');
      span_el.append(' / ');

      return span_el;
    })
    .append(() => {
      let span_el = $('<a class="h1_repo"></a>');
      span_el
        .append(url.getRepo())
        .attr(
          'href',
          [
            url.getHost(),
            url.getGitType(),
            url.getUsername(),
            url.getRepo(),
          ].join('/'),
        );

      return span_el;
    })
    .append(() => {
      let a_element = $('<a class="h1_status"></a>');
      let img_element = $('<img alt="status" src=""/>');

      img_element.attr('src', url.getRepoFullNameUrl() + '/status');
      a_element
        .append(img_element)
        .attr('href', url.getRepoFullNameUrl() + '/getstatus')
        .attr('target', '_black');

      return a_element;
    });

  $('#repo').append(content);

  console.log(type);

  common.column_remove(); // 移除 column
  column_el_click(type, false); // 渲染 display 页面

  if (url.getUrlWithArray().length === 5) {
    return;
  }

  if (url.getUrlWithArray().length === 8) {
    type = url.getUrlWithArray().slice(-2) === 'builds' ? 'build_id' : 'jobs';
  }

  common.column_click_handle(type); // 渲染被点击的 column
  changeUrl(type, true);
});

// 处理回退事件
window.onpopstate = event => {
  let id = event.state.key_id;
  console.log(id);

  column_el_click(id, false); // 渲染 display 页面
  common.column_remove(); // 移除 column
  common.column_click_handle(id); // 渲染被点击的 column
};

// 处理 cancel restart button 点击事件
$(document).on(
  'click',
  '.job_list button,.build_data button,.builds_list button,.pull_requests_list button',
  function() {
    $(this).attr('disabled', 'disabled');

    (async that => {
      await common_status.buttonClick(that);

      let type = url.getType();
      column_el_click(type, false); // 渲染 display 页面
      common.column_remove(); // 移除 column
      common.column_click_handle(type); // 渲染被点击的 column
    })($(this));

    return false;
  },
);
