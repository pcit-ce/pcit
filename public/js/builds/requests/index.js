'use strict';

function display(data) {
  let display_element = $('#display');

  display_element.empty();

  let requests_el = $('<div class="requests"></div>');

  $.each(data, (key, value) => {
    let requests_el_item = $('<div class="requests_list"></div>');

    let {
      id,
      branch,
      commit_id,
      tag,
      commit_message,
      build_status,
      event_type,
      pull_request_number
    } = value;

    requests_el_item
      .append(() => {
        return $('<div class="status"></div>').append(build_status);
      })
      .append(() => {
        return $('<div class="event_type"></div>').append(event_type);
      })
      .append(() => {
        return $('<div class="branch"></div>').append(branch);
      })
      .append(() => {
        return $('<div class="commit_id"></div>').append(
          commit_id.substring(0, 8)
        );
      })
      .append(() => {
        return $('<div class="commit_message"></div>').append(commit_message);
      })
      .append(() => {
        return $('<div class="build_id"></div>').append(id);
      })
      .append(() => {
        return $('<div class="reason"></div>').append(
          build_status === 'skip'
            ? 'Build skipped via commit message'
            : 'Build created successfully '
        );
      });
    requests_el.append(requests_el_item);
  });

  display_element.append(requests_el);
}

module.exports = {
  handle: (url, token) => {
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/requests',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      },
      success: function(data) {
        display(data);
      }
    });
  }
};
