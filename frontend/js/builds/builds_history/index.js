const { column_span_click } = require('../common');
const git = require('../../common/git');
const builds = require('../builds');
const common_status = require('../../common/status');
const build_not_find = require('../error/error').error_info;

function display(data, url) {
  let display_element = $('#display');

  display_element.empty();

  let url_array = url.getUrlWithArray();

  // console.log(url_array);

  if (8 === url_array.length) {
    // 展示某个 build 详情
    if (0 === data.length || 'error' === data) {
      display_element.append(
        build_not_find(
          "Oops, we couldn't find that build!",
          '',
          'The build not exist.',
        ),
      );
      // display_element.innerHeight(55);
    } else {
      // 展示某个 build
      $('#pull_requests').after(() => {
        let span_el = $(
          '<div id="build_id" class="col-md-2 text-center"></div>',
        );
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
    // display_element.innerHeight((i + 1) * 100);
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
        finished_at: stopped_at,
        tag,
      } = status;

      commit_message = tag ? tag : commit_message;

      let commit_url = git.getCommitUrl(
        url.getUsername(),
        url.getRepo(),
        commit_id,
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
        handle: button_handle,
        title: button_title,
      } = common_status.getButton(build_status);
      status_color = common_status.getColor(build_status);
      build_status = common_status.change(build_status);

      // li_el.append(() => {
      //   let div_element = $('<div class="build_id"></div>');
      //   div_element.append('').css({
      //     background: status_color,
      //     border: '1px solid' + status_color,
      //   });
      //
      //   return div_element;
      // });

      li_el
        .css('border-left', '8px solid' + status_color)
        .append($('<div class="event_type"></div>').append(event_type))
        .append(
          $('<div class="branch"></div>')
            .append($('<strong></strong>').append(branch.slice(0, 10)))
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
            .attr({
              href: commit_url,
              title: 'View commit on GitHub',
              target: '_block',
            })
            .addClass('commit_url'),
        )
        .append(
          $('<a class="build_status"></a>')
            .append(
              $('<strong></strong>').append(`#${build_id} ${build_status}`),
            )
            .attr({
              href: `${location.href}/${build_id}`,
              target: '_self',
            })
            .css('color', status_color),
        )
        .append($('<div class="build_time"></div>').append(started_at))
        .append(
          $('<div></div>')
            .append(stopped_at)
            .addClass('build_time_ago')
            .attr('title', 'Finished ' + new Date().toLocaleString()),
        )
        .append(
          $('<button class="cancel_or_restart"></button>')
            .append(
              $('<i class="material-icons"></i>').append(
                button_handle === 'cancel' ? 'cancel' : 'refresh',
              ),
            )
            .attr({
              title: button_title + ' build',
              event_id: build_id,
              job_or_build: 'build',
              handle: button_handle,
            })
            .addClass('btn btn-link'),
        );

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
    display_element.append(build_not_find('Not Build Yet !', '', ''));
  }
}

module.exports = {
  handle: url => {
    let build_id;
    let url_array = url.getUrlWithArray();
    let display_element = $('#display');

    if (8 === url_array.length) {
      build_id = url_array[7];
    } else {
      column_span_click('builds');
    }

    const pcit = require('@pcit/pcit-js');

    const builds = new pcit.Builds('', '');

    if (build_id) {
      // $.ajax({
      //   type: 'GET',
      //   url: '/api/build/' + build_id,
      //   success: function(data) {
      //     display(data, url);
      //   },
      //   error: function(data) {
      //     build_not_find(
      //       "Oops, we couldn't find that build!",
      //       '',
      //       'The build may not exist or may belong to another repository.',
      //     );
      //     // console.log(data);
      //   },
      // });

      (async () => {
        try {
          let result = await builds.find(build_id);
          display(result, url);
        } catch (e) {
          build_not_find(
            "Oops, we couldn't find that build!",
            '',
            'The build may not exist or may belong to another repository.',
          );
        }
      })();

      return;
    }

    // 加载中，动画 TODO

    // display_element.empty().append('加载中...');

    (async () => {
      try {
        let result = await builds.findByRepo(
          url.getGitType(),
          url.getRepoFullName(),
        );
        display(result, url);
      } catch (e) {
        display_element.empty();
        display_element.append(build_not_find('Not Build Yet !', '', ''));
      }
    })();

    // $.ajax({
    //   type: 'GET',
    //   url: '/api/repo/' + url.getGitRepoFullName() + '/builds',
    //   success: function(data) {
    //     display(data, url);
    //   },
    //   error: function(data) {
    //     display_element.empty();
    //     display_element.append(build_not_find('Not Build Yet !', '', ''));
    //     // console.log(data);
    //   },
    // });
  },
};
