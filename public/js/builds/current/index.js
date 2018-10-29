const {column_span_click} = require('../common');
const log = require('../log');

function display(data, username, repo) {
  let display_element = $("#display");

  display_element.empty();

  if (0 === data.length) {
    display_element.append("Not Build Yet !");
  } else {
    log.show(data, username, repo);
  }
}

module.exports = {
  handle: (git_repo_full_name, username, repo) => {
    column_span_click('current');

    $.ajax({
      type: "GET",
      url: '/api/repo/' + git_repo_full_name + '/build/current',
      success: function (data) {
        display(data, username, repo);
      }
    });
  }
};
