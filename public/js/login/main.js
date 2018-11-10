'use strict';

/* eslint-disable no-undef */

// eslint-disable-next-line no-unused-vars
function hello() {
  document.location = 'https://' + location.host + '/login/hello.html';
}

function github() {
  document.location = 'https://' + location.host + '/oauth/github/login';
}

// eslint-disable-next-line no-unused-vars
function coding() {
  document.location = 'https://' + location.host + '/oauth/coding/login';
}

function gitee() {
  document.location = 'https://' + location.host + '/oauth/gitee/login';
}

// eslint-disable-next-line no-unused-vars
function gogs() {
  document.location = 'https://' + location.host + '/oauth/gogs/login';
}

$('.github-login-button').on({
  click: () => {
    github();
  },
});

$('.gitee-login-button').on({
  click: () => {
    gitee();
  },
});

new Vue({
  el: '.hello-login-button',
  methods: {
    clickMethod: () => {
      alert('即将支持，敬请期待！请使用其他方式登录');
    },
  },
});

new Vue({
  el: '.coding-login-button',
  methods: {
    clickMethod: () => {
      alert('即将支持，敬请期待！请使用其他方式登录');
    },
  },
});

new Vue({
  el: '.gogs-login-button',
  methods: {
    clickMethod: () => {
      alert('即将支持，敬请期待！请使用其他方式登录');
    },
  },
});
