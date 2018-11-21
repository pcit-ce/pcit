'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  display_element.append('手动触发构建功能即将上线');
  //.innerHeight(55);
}

module.exports = {
  handle: (url, token) => {
    console.log(location.href);
    $.ajax({
      type: 'post',
      url: '/api/repo/' + url.getRepoFullName() + '/trigger',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType()),
      },
      success: function(data) {
        display(data);
      },
      error: () => {
        display('');
      },
    });
  },
};
