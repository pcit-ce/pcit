const { column_span_click } = require('../common');
const git = require('../../common/git');
const common_status = require('../../common/status');
const error_info = require('../error/error').error_info;

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  if (0 === data.length) {
    display_element.append(
      error_info('No pull request builds for this repository'),
    );
    // display_element.innerHeight(55);
  } else {
    let ul_el = $('<ul class="pull_requests_list"></ul>');

    // display_element.height((data.length + 1) * 100);

    $.each(data, function(id, status) {
      let {
        pull_request_number: pull_request_id,
        id: build_id,
        branch,
        committer_username,
        commit_message,
        commit_id,
        build_status,
        started_at,
        finished_at: stopped_at,
      } = status;

      let username = url.getUsername();
      let repo = url.getRepo();
      let repo_full_name_url = url.getRepoFullNameUrl();

      let commit_url = git.getCommitUrl(username, repo, commit_id);

      let pull_request_url = git.getPullRequestUrl(
        username,
        repo,
        pull_request_id,
      );

      commit_id = commit_id.substr(0, 7);

      if (null == started_at) {
        started_at = 'Pending';
      } else {
        let d;
        d = new Date(started_at * 1000);
        started_at = d.toLocaleString();
      }

      if (null == stopped_at) {
        stopped_at = 'Pending';
      } else {
        let d;
        d = new Date(stopped_at * 1000);
        stopped_at = d.toLocaleString();
      }

      let status_color;

      let {
        title: button_title,
        handle: button_handle,
      } = common_status.getButton(build_status);
      status_color = common_status.getColor(build_status);
      build_status = common_status.change(build_status);

      let li_el = $('<li></li>');

      li_el
        // .append($('<div class="id"></div>').append())
        // .append(
        //   $('<div class="build_id"></div>')
        //     .append('')
        //     .css({
        //       background: status_color,
        //       border: '1px solid' + status_color,
        //     }),
        // )
        .css('border-left', '8px solid ' + status_color)
        .append(
          $('<a class="pull_request_url"></a>')
            .append(`#PR ${pull_request_id}`)
            .attr('title', 'View pull request on GitHub')
            .attr('href', pull_request_url)
            .attr('target', '_block')
            .css('color', status_color),
        )
        .append(
          $('<div class="branch"></div>')
            .append($('<strong></strong>').append(branch))
            .attr('title', branch)
            .css('color', status_color),
        )
        .append(
          $('<div class="committer"></div>')
            .append(committer_username)
            .attr('title', committer_username),
        )
        .append(
          $('<div class="commit_message"></div>')
            .append(commit_message.slice(0, 40))
            .attr('title', commit_message),
        )
        .append(
          $('<a class="commit_id"></a>')
            .append(commit_id)
            .attr('href', commit_url)
            .attr('target', '_block')
            .attr('title', 'View commit on GitHub'),
        )
        .append(
          $('<a class="build_status"></a>')
            .append(
              $('<strong></strong>').append(
                '#' + build_id + ' ' + build_status,
              ),
            )
            .attr('href', `${repo_full_name_url}/builds/${build_id}`)
            .attr('target', '_self')
            .css('color', status_color),
        )
        .append($('<div class="build_time"></div>').append(started_at))
        .append(() => {
          let date = new Date();
          return $('<div class="build_time_ago"></div>')
            .append(stopped_at)
            .attr('title', 'Finished ' + date.toLocaleString());
        })
        .append(
          $('<button class="cancel_or_restart"></button>')
            .append(
              $('<i class="material-icons"></i>').append(
                button_handle === 'cancel' ? 'cancel' : 'refresh',
              ),
            )
            .attr('handle', button_handle)
            .attr('title', button_title + ' build')
            .attr('event_id', build_id)
            .attr('job_or_build', 'build')
            .addClass('btn btn-link'),
        );

      ul_el.append(li_el);
    });
    display_element.append(ul_el);
  }
}

module.exports = {
  handle: url => {
    column_span_click('pull_requests');

    // $.ajax({
    //   type: 'GET',
    //   url: '/api/repo/' + url.getGitRepoFullName() + '/builds?type=pr',
    //   success: function(data) {
    //     display(data, url);
    //   },
    // });

    const pcit = require('@pcit/pcit-js');

    const builds = new pcit.Builds('', '');

    (async () => {
      let result = await builds.findByRepo(
        url.getGitType(),
        url.getRepoFullName(),
        undefined,
        true,
      );

      display(result, url);
    })();
  },
};
