'use strict';

const { column_span_click } = require('../common');
const git = require('../../common/git');
const builds = require('../builds');
const common_status = require('../../common/status');

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  let url_array = url.getUrlWithArray();

  console.log(url_array);

  if (8 === url_array.length) {
    // 展示某个 build 详情
    if (0 === data.length || 'error' === data) {
      display_element.append("Oops, we couldn't find that build!");
      display_element.innerHeight(55);
    } else {
      // 展示某个 build
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

      builds.show(data, url);
    }
  } else if (0 !== data.length) {
    let i = data.length + 1;
    let ul_el = $('<ul class="builds_list"></ul>');
    display_element.innerHeight((i + 1) * 100);
    // display_element.innerHeight(i * 100);
    $.each(data, function(id, status) {
      i--;

      let {
        event_type,
        id: build_id,
        branch,
        committer_username,
        commit_message,
        commit_id,
        build_status,
        started_at,
        finished_at: stopped_at
      } = status;

      let commit_url = git.getCommitUrl(
        url.getUsername(),
        url.getRepo(),
        commit_id
      );
      commit_id = commit_id.substr(0, 7);

      if (null == started_at) {
        started_at = 'Pending';
      } else {
        let d;
        d = new Date(parseInt(started_at) * 1000);
        started_at = d.toLocaleString();
      }

      if (null == stopped_at) {
        stopped_at = 'Pending';
      } else {
        let d;
        d = new Date(parseInt(stopped_at) * 1000);
        stopped_at = d.toLocaleString();
      }

      let li_el = $('<li></li>');

      let status_color;

      let {
        class: button_class,
        handle: button_handle,
        title: button_title
      } = common_status.getButton(build_status);
      status_color = common_status.getColor(build_status);
      build_status = common_status.change(build_status);

      li_el.append(() => {
        let div_element = $('<div class="build_id"></div>');
        div_element.append('').css({
          background: status_color,
          border: '1px solid' + status_color
        });

        return div_element;
      });

      li_el
        .append(() => {
          let div_element = $('<div class="event_type"></div>');
          div_element.append(event_type);

          return div_element;
        })
        .append(() => {
          let div_element = $('<div class="branch"></div>');
          div_element
            .append(branch.slice(0, 10))
            .attr('title', branch)
            .css('color', status_color);

          return div_element;
        })
        .append(() => {
          let div_el = $('<div class="committer"></div>');
          div_el.append(committer_username).attr('title', committer_username);

          return div_el;
        })
        .append(() => {
          let div_element = $('<div class="commit_message"></div>');
          div_element
            .append(commit_message.slice(0, 40))
            .attr('title', commit_message);

          return div_element;
        })
        .append(() => {
          let a_element = $('<a class="commit_id"></a>');
          a_element
            .append(commit_id)
            .attr({
              href: commit_url,
              title: 'View commit on GitHub',
              target: '_block'
            })
            .addClass('commit_url');

          return a_element;
        })
        .append(() => {
          let a_element = $('<a class="build_status"></a>');
          a_element
            .append(`#${build_id} ${build_status}`)
            .attr({
              href: `${location.href}/${build_id}`,
              target: '_self'
            })
            .css('color', status_color);

          return a_element;
        })
        .append(() => {
          let div_element = $('<div class="build_time"></div>');
          div_element.append(started_at);

          return div_element;
        })
        .append(() => {
          let div_element = $('<div></div>');
          let data = new Date();

          div_element
            .append(stopped_at)
            .addClass('build_time_ago')
            .attr('title', 'Finished ' + data.toLocaleString());

          return div_element;
        })
        .append(() => {
          return (() => {
            return $('<button class="cancel_or_restart"></button>')
              .append(() => {
                return $('<i class="material-icons"></i>').append(() => {
                  return button_handle === 'cancel' ? 'cancel' : 'refresh';
                });
              })
              .attr({
                title: button_title + ' build',
                event_id: build_id,
                job_or_build: 'build',
                handle: button_handle
              })
              .addClass('btn btn-light');
          })();
        });

      ul_el.append(li_el);
    });
    display_element.append(ul_el);

    // 按钮点击事件 already move to main.js
    // $('.builds_list button').on({
    //   'click': function () {
    //     common_status.buttonClick($(this));
    //   }
    // })
  } else {
    display_element.append('Not Build Yet !');
  }
}

module.exports = {
  handle: url => {
    let build_id;
    let url_array = url.getUrlWithArray();

    if (8 === url_array.length) {
      build_id = url_array[7];
    } else {
      column_span_click('builds');
    }

    if (build_id) {
      $.ajax({
        type: 'GET',
        url: '/api/build/' + build_id,
        success: function(data) {
          display(data, url);
        },
        error: function(data) {
          display('error');
          console.log(data);
        }
      });

      return;
    }

    $.ajax({
      type: 'GET',
      url: '/api/repo/' + url.getGitRepoFullName() + '/builds',
      success: function(data) {
        display(data, url);
      },
      error: function(data) {
        display('error');
        console.log(data);
      }
    });
  }
};
