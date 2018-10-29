const {column_span_click, column_click_handle} = require('../common');
const git = require('../../common/git');
const log = require('../log');

function display(data, username, repo, url_array) {
  let display_element = $("#display");

  display_element.empty();

  url_array = url_array();

  console.log(url_array);

  if (8 === url_array.length) {
    if (0 === data.length || 'error' === data) {
      display_element.append('Oops, we couldn\'t find that build!');
    } else {
      let column_el = $('#pull_requests');
      column_el.after('<span id="column_ico"> > <span>');

      column_el = $('#column_ico');

      column_el.after(() => {
        let span_el = $('<span id="build_id"></span>');
        span_el.append('Build #' + data.id);

        return span_el;

      });

      // build_id span 元素被选中
      $('#build_id').trigger('click');

      log.show(data);
    }

  } else if (0 !== data.length) {
    let i = data.length + 1;
    let ul_el = $('<ul class="builds_list"></ul>');
    ul_el.innerHeight(i * 100);
    $.each(data, function (id, status) {
      i--;

      let {
        event_type, id: build_id, branch, committer_username,
        commit_message, commit_id, build_status, started_at, finished_at: stopped_at
      } = status;

      let commit_url = git.getCommitUrl(username, repo, commit_id);
      commit_id = commit_id.substr(0, 7);

      if (null == started_at) {
        started_at = 'Pending'
      } else {
        let d;
        d = new Date(parseInt(started_at) * 1000);
        started_at = d.toLocaleString();
      }

      if (null == stopped_at) {
        stopped_at = 'Pending'
      } else {
        let d;
        d = new Date(parseInt(stopped_at) * 1000);
        stopped_at = d.toLocaleString();
      }

      let li_el = $('<li></li>');

      li_el.append(() => {
        let div_element = $('<div class="build_id"></div>');
        div_element.append('');

        if (build_status === 'success') {
          div_element.css('background', '#39aa56');
        } else if (build_status === 'in_progress') {
          div_element.css('background', 'yellow');
        } else {
          div_element.css('background', '#db4545');
        }

        return div_element;
      });

      li_el.append(() => {
        let div_element = $('<div class="event_type"></div>');
        div_element.append(event_type);

        return div_element;
      }).append(() => {
        let div_element = $('<div class="branch"></div>');
        div_element.append(branch.slice(0, 10)).attr('title', branch);

        return div_element;
      }).append(() => {
        let div_el = $('<div class="committer"></div>');
        div_el.append(committer_username);

        return div_el;
      }).append(() => {
        let div_element = $('<div class="commit_message"></div>');
        div_element.append(commit_message.slice(0, 28)).attr('title', commit_message);

        return div_element;
      }).append(() => {
        let a_element = $('<a class="commit_id"></a>');
        a_element.append(commit_id);
        a_element.attr('href', commit_url).attr('title', 'View commit on GitHub');
        a_element.attr('target', '_block').addClass('commit_url');

        return a_element;
      }).append(() => {
        let a_element = $('<a class="build_status"></a>');
        a_element.append(`#${build_id} ${build_status}`);
        a_element.attr('href', `${location.href}/${build_id}`);
        a_element.attr('target', '_block');

        return a_element;
      }).append(() => {
        let div_element = $('<div class="build_time"></div>');
        div_element.append(started_at);

        return div_element;
      }).append(() => {
        let div_element = $('<div></div>');
        div_element.append(stopped_at).addClass('build_time_ago');
        div_element.attr('title', 'Finished ');

        return div_element;
      }).append(() => {
        return (() => {
          let button_el = $('<button class="cancel_or_restart"></button>');
          button_el.append('button');

          return button_el;
        })();
      });

      ul_el.append(li_el);
    });
    display_element.append(ul_el);
  } else {
    display_element.append('Not Build Yet !');
  }
}

module.exports = {
  handle: (git_repo_full_name, username, repo, url_array) => {

    let build_id;

    if (8 === url_array().length) {
      build_id = url_array()[7];
    } else {
      column_span_click('builds');
    }

    console.log(url_array());

    if (build_id) {
      $.ajax({
        type: 'GET',
        url: '/api/build/' + build_id,
        success: function (data) {
          display(data, username, repo, url_array);
        },
        error: function (data) {
          display('error');
          console.log(data);
        }
      });

      return;
    }

    $.ajax({
      type: 'GET',
      url: '/api/repo/' + git_repo_full_name + '/builds',
      success: function (data) {
        display(data, username, repo, url_array);
      },
      error: function (data) {
        display('error');
        console.log(data);
      }
    });
  },
};
