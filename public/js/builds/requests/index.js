'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();
  display_element.append('requests' + JSON.stringify(data));
}

module.exports = {
  handle: (url, token) => {
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/requests',
      headers: {
        'Authorization': 'token ' + token.getToken(url.getGitType())
      },
      success: function (data) {
        display(data);
      }
    });
  },
};
