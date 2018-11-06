'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  display_element.append('缓存列表功能即将上线');
}

module.exports = {
  handle: (url, token) => {
    console.log(location.href);
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/caches',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      },
      success: function(data) {
        display(data);
      }
    });
  }
};
