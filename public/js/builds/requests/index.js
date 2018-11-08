'use strict';

const status = require('../../common/status');
const git = require('../../common/git');

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  let requests_el = $('<div class="requests"></div>');

  if (data.length === 0) {
    display_element.append('Not Event receive !');
  }

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
      pull_request_number,
      created_at
    } = value;

    let color =
      build_status === 'skip'
        ? status.getColor('error')
        : status.getColor('success');

    requests_el_item
      .append(() => {
        return $('<div class="status"></div>').css(
          'border-left',
          '8px solid ' + color
        );
      })
      .append(() => {
        return $('<div class="event_type"></div>').append(event_type);
      })
      .append(() => {
        return $('<div class="branch"></div>')
          .append(branch.substring(0, 10))
          .attr('title', branch)
          .css('color', color);
      })
      .append(() => {
        let commit_url = [
          git.getCommitUrl(
            url.getUsername(),
            url.getRepo(),
            commit_id,
            url.getGitType()
          )
        ].join('/');

        return $('<a class="commit_id"></a>')
          .append(commit_id.substring(0, 8))
          .attr({
            title: 'View commit on GitHub',
            href: commit_url,
            target: '_blank'
          })
          .css('color', color);
      })
      .append(() => {
        let date = new Date();

        let time = (date.valueOf() / 1000 - created_at) / 24 / 60 / 60;

        let day = time > 1 ? Math.round(time) : '1';

        return $('<div class="created_at"></div>')
          .append(day + ' days ago')
          .attr('title', new Date(created_at * 1000).toLocaleString());
      })
      .append(() => {
        return $('<div class="commit_message"></div>')
          .append(commit_message.substring(0, 35))
          .attr('title', commit_message);
      })
      .append(() => {
        let build_id_url =
          '/' +
          [url.getGitType(), url.getRepoFullName(), 'builds', id].join('/');

        return $('<a class="build_id"></a>')
          .append('# ' + id)
          .attr({
            title: 'Go to the build this request triggered',
            href: build_id_url
          });
      })
      .append(() => {
        let message =
          build_status === 'skip'
            ? 'Build skipped via commit message'
            : 'Build created successfully';

        return $('<div class="reason"></div>')
          .append(message.substring(0, 26))
          .attr({ title: message });
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
        display(data, url);
      },
      error: () => {
        display('', url);
      }
    });
  }
};