'use strict';

function new_header() {}

function new_footer() {}

function display(data) {
  console.log(data);
}

function request(git_type, username, token) {
  (async () => {
    let username_repo_data = await new Promise(resolve => {
      $.ajax({
        url: '/api/repos/' + [git_type, username].join('/'),
        type: 'GET',
        success(data) {
          resolve(data);
        },
      });
    });

    display(username_repo_data);
  })();
}

module.exports = {
  handle(git_type, username, token) {
    $('header').remove();
    $('footer').remove();
    $('display').remove();
    $('#repo').remove();
    $('.column').remove();
    $('br').remove();
    new_header();
    new_footer();
    request(git_type, username, token);
  },
};
