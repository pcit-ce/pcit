const { column_span_click } = require('../common');

const build = require('../builds');
const error_info = require('../error/error').error_info;

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  if (0 === data.length) {
    display_element.append(error_info('Not Build Yet !'));
    // display_element.innerHeight(55);
  } else {
    build.show(data, url);
  }
}

module.exports = {
  handle: url => {
    column_span_click('current');

    const pcit = require('@pcit/pcit-js');

    const builds = new pcit.Builds('', '');

    (async () => {
      try {
        let result = await builds.current(
          url.getGitType(),
          url.getRepoFullName(),
        );

        display(result, url);
      } catch (e) {
        display('', url);
      }
    })();

    // $.ajax({
    //   type: 'GET',
    //   url: '/api/repo/' + url.getGitRepoFullName() + '/build/current',
    //   success: function(data) {
    //     display(data, url);
    //   },
    //   error: function() {
    //     display('', url);
    //   },
    // });
  },
};
