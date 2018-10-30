'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  display_element.append(JSON.stringify(data));
}

module.exports = {
  handle: (git_repo_full_name) => {
    console.log('jobs');
    $.ajax({
      type: 'get',
      url: '/api/repo/' + git_repo_full_name + '/jobs/1',
      success: function (data) {
        display(data);
      }
    });
  }
};
