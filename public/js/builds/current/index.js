'use strict';

const { column_span_click } = require('../common');
const build = require('../builds');

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  if (0 === data.length) {
    display_element.append('Not Build Yet !');
    display_element.innerHeight(55);
  } else {
    build.show(data, url);
  }
}

module.exports = {
  handle: url => {
    column_span_click('current');

    $.ajax({
      type: 'GET',
      url: '/api/repo/' + url.getGitRepoFullName() + '/build/current',
      success: function(data) {
        display(data, url);
      },
      error: function() {
        display('', url);
      }
    });
  }
};
