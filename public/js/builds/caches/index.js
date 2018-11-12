'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  let cache_el = $('<div class="cache container"></div>').append(
    '缓存列表功能即将上线',
  );

  display_element.append(cache_el);
  // .innerHeight(55);
}

module.exports = {
  handle: (url, token) => {
    console.log(location.href);
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/caches',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType()),
      },
      success: function(data) {
        display(data);
      },
    });
  },
};
