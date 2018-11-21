'use strict';

const common_status = require('../../common/status');
const git = require('../../common/git');

module.exports = {
  show: (data, url, job = false) => {
    console.log(data);
    let display_element = $('#display');

    let {
      id,
      status,
      commit_id,
      commit_message,
      branch,
      committer_name,
      compare,
      stopped_at,
      env_vars,
    } = data;

    let status_color;

    let {
      handle: button_handle,
      title: button_title,
    } = common_status.getButton(status);
    status_color = common_status.getColor(status);
    let build_status = common_status.change(status);

    console.log(build_status);
    if (null === stopped_at) {
      stopped_at = 'This build is ' + build_status;
    } else {
      let d;
      d = new Date(parseInt(stopped_at) * 1000);
      stopped_at = d.toLocaleString();
    }

    let commit_url = git.getCommitUrl(
      url.getUsername(),
      url.getRepo(),
      commit_id,
    );

    let div_element = $('<div class="build_data"></div>').css(
      'border-left',
      '8px solid ' + status_color,
    );

    // div_element.append(() => {
    //   let build_id_element = $('<div class="build_id"></div>');
    //   build_id_element.append('').css({
    //     background: status_color,
    //     border: '1px solid ' + status_color,
    //   });
    //   return build_id_element;
    // });

    div_element
      .append(
        $('<div class="branch"></div>')
          .append($('<strong></strong>').append(branch.slice(0, 7)))
          .attr('title', branch)
          .css('color', status_color),
      )
      .append(
        $('<a class="branch_url">Branch </a>')
          .append(branch)
          .attr('href', '')
          .attr('target', '_block')
          .attr('title', 'View branch on GitHub'),
      )
      .append(
        $('<div class="build_status"></div>')
          .append($('<strong></strong>').append('#' + id + ' ' + build_status))
          .css('color', status_color),
      )
      .append(
        $('<a class="commit_url">Commit </a>')
          .append(commit_id.slice(0, 7))
          .attr('title', 'View commit on GitHub')
          .attr('href', commit_url)
          .attr('target', '_blank'),
      );

    div_element.append(
      $('<div class="commit_message"></div>')
        .append(commit_message)
        .attr('title', commit_message)
        .css('color', status_color),
    );

    div_element.append(
      $('<div class="committer"></div>')
        .append(committer_name)
        .attr('title', committer_name),
    );

    // div_element.append(
    //   $('<a class="compare"></a>')
    //     .append('Compare')
    //     .attr({
    //       title: 'View diff on GitHub',
    //       href: compare,
    //       target: '_blank',
    //     }),
    // );

    div_element
      .append(
        $('<div class="build_time"></div>').append('Ran for 7 min 17 sec'),
      )
      .append(
        $('<div class="build_time_ago"></div>').append('about 9 hours ago'),
      )
      .append(
        $('<button class="cancel_or_restart"></button>')
          .append(
            $('<i></i>')
              .addClass('material-icons')
              .append(() => {
                return button_handle === 'cancel' ? 'cancel' : 'refresh';
              }),
          )
          .attr('handle', button_handle)
          .attr('title', button_title + (job ? ' job' : ' build'))
          .attr('event_id', id)
          .attr('job_or_build', job ? 'job' : 'build')
          .addClass('btn btn-link'),
      )
      .append($('<div class="env"></div>').append(env_vars));

    display_element.append(div_element);
  },
};
