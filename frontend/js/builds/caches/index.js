const error_info = require('../error/error').error_info;

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  display_element.append(error_info('缓存列表功能即将上线'));
  // .innerHeight(55);
}

module.exports = {
  handle: (url, token) => {
    // console.log(location.href);
    // $.ajax({
    //   type: 'get',
    //   url: '/api/repo/' + url.getRepoFullName() + '/caches',
    //   headers: {
    //     Authorization: 'token ' + token.getToken(url.getGitType()),
    //   },
    //   success: function(data) {
    //     display(data);
    //   },
    // });

    const pcit = require('@pcit/pcit-js');
    const pcit_cache = new pcit.Caches(token.getToken(url.getGitType()), '');

    (async () => {
      let result = await pcit_cache.list(url.getRepoFullName());

      display(result);
    })();
  },
};
