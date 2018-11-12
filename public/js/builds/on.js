'use strict';

const url = require('./url');
const common = require('./common');
const title = require('./title');
const token = require('../common/token');

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
        Authorization: 'token ' + token.getToken(url.getGitType()),
      },
      data: `{"${that.attr('name')}":${that.prop('value')}}`,
      url:
        '/api/repo/' +
        [url.getRepoFullName(), 'setting', that.attr('name')].join('/'),
    });
  },
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
    return new Promise(resolve => {
      $.ajax({
        type: 'delete',
        url:
          '/api/repo/' + [url.getRepoFullName(), 'env_var', env_id].join('/'),
        headers: {
          Authorization: 'token ' + token.getToken(url.getGitType()),
        },
        success() {
          resolve();
        },
      });
    });
  })().then(() => {
    // let display_el = $('#display');
    // display_el.innerHeight(display_el.innerHeight() - 50);
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
    return new Promise(resolve => {
      $.ajax({
        type: 'post',
        data: `{"env_var.name":"${name}","env_var.value":"${value}","env_var.public":"${is_public}"}`,
        url: '/api/repo/' + [url.getRepoFullName(), 'env_vars'].join('/'),
        headers: {
          Authorization: 'token ' + token.getToken(url.getGitType()),
        },
        success: res => {
          resolve(res);
        },
      });
    });
  }

  (async () => {
    let id = await getData();
    console.log(id);
    // 增加列表
    let env_el = $('.env_list_item:nth-last-of-type(2)');

    let env_item_el = $('<form class="env_list_item form-inline"></form>').attr(
      {
        env_id: id,
        public: is_public,
      },
    );

    env_item_el
      .append(() => {
        return $(
          '<input class="env_name form-control" type="text" readonly/>',
        ).attr('placeholder', name);
      })
      .append(() => {
        return $(
          '<input class="env_value form-control" type="text" readonly/>',
        ).attr('placeholder', value);
      })
      .append(() => {
        return $(
          '<button class="delete btn btn-light btn-xs"></button>',
        ).append('Delete');
      });

    env_el.after(env_item_el);

    // let display_el = $('#display');
    // display_el.innerHeight(display_el.innerHeight() + 50);
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
          '/',
        ),
      data: `{"maximum_number_of_builds":${value}}`,
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType()),
      },
    });
  },
);

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
    common.column_click_handle(id);
  }

  title.show(url.getBaseTitle(), id);
});
