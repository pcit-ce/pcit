'use strict';

const {column_span_click} = require('../common');
const git = require('../../common/git');

function display(data, username, repo, repo_full_name_url) {
  let display_element = $('#display');

  display_element.empty();

  if (0 === data.length) {
    display_element.append('No pull request builds for this repository');
  } else {

    let ul_el = $('<ul class="pull_requests_list"></ul>');

    ul_el.height((data.length + 1) * 100);

    $.each(data, function (id, status) {

      let {
        pull_request_number: pull_request_id, id: build_id, branch, committer_username,
        commit_message, commit_id, build_status, started_at, finished_at: stopped_at,
      } = status;

      let commit_url = git.getCommitUrl(username, repo, commit_id);

      let pull_request_url = git.getPullRequestUrl(username, repo, pull_request_id);

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

      let li_el = $('<li></li>');

      li_el.append(() => {
        let div_el = $('<div class="id"></div>');
        div_el.append();

        return div_el;
      }).append(() => {
        let div_el = $('<div class="build_id"></div>');
        div_el.append('');

        if (build_status === 'success') {
          div_el.css('background', '#39aa56');
        } else if (build_status === 'in_progress') {
          div_el.css('background', 'yellow');
        } else {
          div_el.css('background', '#db4545');
        }

        return div_el;
      }).append(() => {
        let a_el = $('<a class="pull_request_url"></a>');
        a_el.append(`#PR ${pull_request_id}`);
        a_el.attr('title', 'View pull request on GitHub');
        a_el.attr('href', pull_request_url);
        a_el.attr('target', '_block');

        return a_el;
      }).append(() => {
        let div_el = $('<div class="branch"></div>');
        div_el.append(branch);

        return div_el;
      }).append(() => {
        let div_el = $('<div class="committer"></div>');
        div_el.append(committer_username);

        return div_el;
      }).append(() => {
        let div_el = $('<div class="commit_message"></div>');
        div_el.append(commit_message);

        return div_el;
      }).append(() => {
        let a_el = $('<a class="commit_id"></a>');
        a_el.append(commit_id);
        a_el.attr('href', commit_url);
        a_el.attr('target', '_block');
        a_el.attr('title', 'View commit on GitHub');
        return a_el;
      }).append(() => {
        let a_el = $('<a class="build_status"></a>');
        a_el.append(build_status);
        a_el.attr('href', `${repo_full_name_url}/builds/${build_id}`);
        a_el.attr('target', '_self');

        return a_el;
      }).append(() => {
        let div_el = $('<div class="build_time"></div>');
        div_el.append(started_at);

        return div_el;
      }).append(() => {
        let div_el = $('<div class="build_time_ago"></div>');

        div_el.append(stopped_at);

        return div_el;
      }).append(() => {
        let button_el = $('<button class="cancel_or_restart"></button>');
        button_el.append('button');

        return button_el;
      });

      ul_el.append(li_el);

    });
    display_element.append(ul_el);
  }
}


module.exports = {
  handle: (git_repo_full_name, username, repo, repo_full_name_url) => {
    column_span_click('pull_requests');

    $.ajax({
      type: 'GET',
      url: '/api/repo/' + git_repo_full_name + '/builds?type=pr',
      success: function (data) {
        display(data, username, repo, repo_full_name_url);
      }
    });
  },
};
