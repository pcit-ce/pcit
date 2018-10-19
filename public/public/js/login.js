function hello() {
  document.location = 'https://' + location.host + '/login/hello.html';
}

function github() {
  document.location = 'https://' + location.host + '/oauth/github/login';
}

function coding() {
  document.location = 'https://' + location.host + '/oauth/coding/login';
}

function gitee() {
  document.location = 'https://' + location.host + '/oauth/gitee/login';
}

function gogs() {
  document.location = 'https://' + location.host + '/oauth/gogs/login';
}

new Vue({
  el: "#hello",
  data: {
    display: false
  }
});
