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
      env_vars
    } = data;

    let status_color;

    let { title: button_title, text: button_text } = common_status.getButton(
      status
    );
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
      commit_id
    );
    let div_element = $('<div class="build_data"></div>');

    div_element.append(() => {
      let build_id_element = $('<div class="build_id"></div>');
      build_id_element.append('').css('background', status_color);
      return build_id_element;
    });

    div_element
      .append(() => {
        let branch_element = $('<div class="branch"></div>');
        branch_element.append(branch).css('color', status_color);

        return branch_element;
      })
      .append(() => {
        let div_el = $('<a class="branch_url">Branch </a>');
        div_el.append(branch);
        div_el.attr('href', '');
        div_el.attr('target', '_block');
        div_el.attr('title', 'View branch on GitHub');
        return div_el;
      })
      .append(() => {
        let build_status_element = $('<div class="build_status"></div>');
        build_status_element
          .append('#' + id + ' ' + build_status)
          .css('color', status_color);

        return build_status_element;
      })
      .append(() => {
        let commit_url_element = $('<a class="commit_url">Commit </a>');
        commit_url_element.append(commit_id.slice(0, 7));
        commit_url_element.attr('title', 'View commit on GitHub');
        commit_url_element.attr('href', commit_url);
        commit_url_element.attr('target', '_blank');

        return commit_url_element;
      });

    div_element.append(() => {
      let commit_message_element = $('<div class="commit_message"></div>');
      commit_message_element.append(commit_message).css('color', status_color);

      return commit_message_element;
    });

    div_element.append(() => {
      let committer_name_element = $('<div class="committer"></div>');
      committer_name_element.append(committer_name);

      return committer_name_element;
    });

    div_element.append(() => {
      let compare_element = $('<a class="compare">Compare </a>');
      compare_element
        .append('Compare')
        .attr('title', 'View diff on GitHub')
        .attr('href', compare);
      compare_element.attr('target', '_blank');

      return compare_element;
    });

    div_element
      .append(() => {
        let stopped_at_element = $('<div class="build_time"></div>');
        stopped_at_element.append('Ran for 7 min 17 sec');

        return stopped_at_element;
      })
      .append(() => {
        let div_el = $('<div class="build_time_ago"></div>');
        div_el.append('about 9 hours ago');

        return div_el;
      })
      .append(() => {
        let button_el = $('<button class="cancel_or_restart"></button>');
        button_el
          .append(button_text)
          .attr('title', button_title + (job ? ' job' : ' build'))
          .attr('event_id', id)
          .attr('type', job ? 'job' : 'build');

        return button_el;
      })
      .append(() => {
        let env_el = $('<div class="env"></div>');
        env_el.append(env_vars);
        console.log(env_vars);
        return env_el;
      });

    display_element.append(div_element);
  }
};
