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

const common = require('./common');
const common_status = require('../common/status');
const token = require('../common/token');

header.show();
footer.show();

// 事件捕获 从父元素到子元素传递
// 事件冒泡 点击了 子元素 会向上传递 即也点击了父元素
$('.column').click(function(event) {
  let id = event.target.id;

  console.log('事件冒泡 ' + id);

  if (id === 'more_options') {
    return;
  }

  if (id === 'build_id') {
    // build_id 元素被点击
    common.column_click_handle(event.target.id);
  }

  title.show(url.getBaseTitle(), id);
});

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
        url.getRepoFullNameUrl() + '/' + id
      );
      return;
    }

    history.pushState(
      { key_id: id },
      null,
      url.getRepoFullNameUrl() + '/' + id
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
  }
});

$('#more_options').on({
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
  }
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
      let span_el = $("<a class='h1_git_type'></a>");
      span_el
        .append(git.format(url.getGitType()))
        .attr(
          'href',
          [
            git.getUrl(url.getUsername(), url.getRepo(), url.getGitType()),
            url.getUsername(),
            url.getRepo()
          ].join('/')
        )
        .attr('target', '_block');

      return span_el;
    })
    .append(() => {
      let span_el = $('<a class="h1_username">');
      span_el
        .append(url.getUsername())
        .attr(
          'href',
          [url.getHost(), url.getGitType(), url.getUsername()].join('/')
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
            url.getRepo()
          ].join('/')
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
    (async that => {
      await common_status.buttonClick(that);

      let type = url.getType();
      column_el_click(type, false); // 渲染 display 页面
      common.column_remove(); // 移除 column
      common.column_click_handle(type); // 渲染被点击的 column
    })($(this));

    return false;
  }
);

$(document).on(
  'click',
  '.setting [name="build_pushes"],' +
    '.setting [name="build_pull_requests"],' +
    '.setting [name="auto_cancel_branch_builds"],' +
    '.setting [name="auto_cancel_pull_request_builds"]',
  function() {
    let that = $(this);

    that.attr('value') === '1'
      ? that.prop('checked', false).prop('value', '0')
      : that.prop('checked', true).prop('value', '1');

    // console.log(that);

    // 发起请求
    $.ajax({
      type: 'patch',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      },
      data: `{"${that.attr('name')}":${that.prop('value')}}`,
      url:
        '/api/repo/' +
        [url.getRepoFullName(), 'setting', that.attr('name')].join('/')
    });
  }
);

$(document).on('click', '.env_list_item .delete', function() {
  let env_id = $(this)
    .parent()
    .attr('env_id');
  $(this)
    .parent()
    .remove();

  // 发起请求
  (() => {
    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'delete',
        url:
          '/api/repo/' + [url.getRepoFullName(), 'env_var', env_id].join('/'),
        headers: {
          Authorization: 'token ' + token.getToken(url.getGitType())
        },
        success() {
          resolve();
        }
      });
    });
  })().then(() => {
    let display_el = $('#display');

    display_el.innerHeight(display_el.innerHeight() - 50);
  });

  return false;
});

$(document).on('click', '.new_env input[name="is_public"]', function() {
  let that = $(this);

  console.log(that.attr('value') === '0');

  that.attr('value') === '0'
    ? that.prop('checked', 'checked').prop('value', '1')
    : that.prop('checked', false).prop('value', '0');

  console.log(that.prop('checked'));
});

$(document).on('click', '.new_env button', function() {
  let is_public = $(this)
    .prev()
    .children()
    .attr('value');
  let value = $(this)
    .prev()
    .prev()
    .val();
  let name = $(this)
    .prev()
    .prev()
    .prev()
    .val();

  console.log(is_public);
  console.log(value);
  console.log(name);

  // 发起请求
  function getData() {
    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'post',
        data: `{"env_var.name":"${name}","env_var.value":"${value}","env_var.public":"${is_public}"}`,
        url: '/api/repo/' + [url.getRepoFullName(), 'env_vars'].join('/'),
        headers: {
          Authorization: 'token ' + token.getToken(url.getGitType())
        },
        success: res => {
          resolve(res);
        }
      });
    });
  }

  (async () => {
    let id = await getData();
    console.log(id);
    // 增加列表
    let env_el = $('.env_list_item:last-of-type');

    let env_item_el = $('<div class="env_list_item"></div>').attr({
      env_id: id,
      public: is_public
    });

    env_item_el
      .append(() => {
        return $('<div class="env_name"></div>').append(name);
      })
      .append(() => {
        return $('<div class="env_value"></div>').append(value);
      })
      .append(() => {
        return $('<button class="delete"></button>').append('Delete');
      });

    env_el.after(env_item_el);

    let display_el = $('#display');

    display_el.innerHeight(display_el.innerHeight() + 50);
  })().then();

  return false;
});

$(document).on(
  'input porpertychange',
  '.general input[name="maximum_number_of_builds"]',
  function() {
    let value = $(this).val();

    if (value.length === 0) {
      return;
    }

    if (value <= 0) {
      alert('value must lt 0');
      return;
    }

    $.ajax({
      type: 'patch',
      url:
        '/api/repo/' +
        [url.getRepoFullName(), 'setting', 'maximum_number_of_builds'].join(
          '/'
        ),
      data: `{"maximum_number_of_builds":${value}}`,
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      }
    });
  }
);
