const status = require('../../common/status');
const git = require('../../common/git');
const error_info = require('../error/error').error_info;

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  // let requests_el = $('<div class="requests"></div>');

  if (data.length === 0) {
    display_element.append(error_info('Not Event receive !'));
    // display_element.innerHeight(55);
    return;
  }

  // display_element.innerHeight(60 + data.length * 30);

  $.each(data, (key, value) => {
    let requests_el_item = $('<div class="row requests_list"></div>');

    let {
      id,
      branch,
      commit_id,
      tag,
      commit_message,
      build_status,
      event_type,
      pull_request_number,
      created_at,
    } = value;

    commit_message = tag ? tag : commit_message;

    let color =
      build_status === 'skip'
        ? status.getColor('error')
        : status.getColor('success');

    requests_el_item
      .css('border-left', '8px solid' + color)
      .append(
        $('<div class="event_type col-md-1"></div>').append(
          event_type === 'pull_request' ? 'pr' : event_type,
        ),
      )
      .append(() => {
        return $('<div class="branch col-md-1"></div>')
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
            url.getGitType(),
          ),
        ].join('/');

        return $('<a class="commit_id col-md-1 text-truncate"></a>')
          .append(commit_id.substring(0, 8))
          .attr({
            title: 'View commit on GitHub',
            href: commit_url,
            target: '_blank',
          })
          .css('color', color);
      })
      .append(() => {
        let date = new Date();

        let time = (date.valueOf() / 1000 - created_at) / 24 / 60 / 60;

        let day = time > 1 ? Math.round(time) : '1';

        return $('<div class="created_at col-md-2"></div>')
          .append(day + ' days ago')
          .attr('title', new Date(created_at * 1000).toLocaleString());
      })
      .append(() => {
        return $('<div class="commit_message col-md-3 text-truncate"></div>')
          .append(commit_message)
          .attr('title', commit_message);
      })
      .append(() => {
        let build_id_url =
          '/' +
          [url.getGitType(), url.getRepoFullName(), 'builds', id].join('/');

        return $('<a class="build_id col-md-1"></a>')
          .append('# ' + id)
          .attr({
            title: 'Go to the build this request triggered',
            href: build_id_url,
          });
      })
      .append(() => {
        let message =
          build_status === 'skip'
            ? 'Build skipped via commit message'
            : 'Build created successfully';

        return $('<div class="reason col-md-3 text-truncate"></div>')
          .append(message.substring(0, 26))
          .attr({ title: message });
      });
    display_element.append(requests_el_item);
  });

  // display_element.append(requests_el);
}

module.exports = {
  handle: (url, token) => {
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/requests',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType()),
      },
      success: function(data) {
        display(data, url);
      },
      error: () => {
        display('', url);
      },
    });
  },
};
