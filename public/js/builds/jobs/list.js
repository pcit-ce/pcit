'use strict';

const common_status = require('../../common/status');

// list builds all jobs

module.exports = {
  show: (data, url) => {
    let jobs_list_el = $('<div class="jobs_list"></div>');

    let { jobs } = data;

    let git_type = url.getGitType();

    let job_url =
      '/' + [git_type, url.getUsername(), url.getRepo(), 'jobs'].join('/');

    $.each(jobs, (index, job) => {
      let { id, state, env_vars = '' } = job;

      env_vars = env_vars ? env_vars : 'no matrix environment set';

      let status_color = common_status.getColor(state);
      let status_background_color = common_status.getColor(state, true);
      let { text: button_text, title: button_title } = common_status.getButton(
        state
      );

      let a_el = $('<a class="job_list"></a>');

      a_el
        .append(() => {
          let div_el = $('<div class="job_id"></div>');
          div_el.append('# ' + id);
          div_el.css('color', status_color);

          return div_el;
        })
        .append(() => {
          let div_el = $('<div class="job_os"></div>');
          div_el.append('Linux');

          return div_el;
        })
        .append(() => {
          let div_el = $('<div class="job_env_vars"></div>');
          div_el
            .append(env_vars && env_vars.slice(0, 50))
            .attr('title', env_vars);

          return div_el;
        })
        .append(() => {
          let div_el = $('<div class="job_run_time"></div>');
          div_el.append('run time');

          return div_el;
        })
        .append(() => {
          let button_el = $('<button class="job_cancel_or_restart"/>');

          button_el
            .append(button_text)
            .attr('title', button_title + ' job')
            .attr('event_id', id)
            .attr('type', 'job');

          return button_el;
        })
        .attr('href', job_url + '/' + id)
        .css('cursor', 'hand')
        .attr('status_background_color', status_background_color)
        .attr('status_color', status_color);

      jobs_list_el.append(a_el);
    });

    let display_el = $('#display');
    display_el.append(jobs_list_el);

    // display_el.css('height', (jobs.length * 10) + 'px');

    // 鼠标移入 job list 背景变色
    $('.job_list').on({
      mousemove: function() {
        let that = $(this);
        let background_color = that.attr('status_background_color');
        let border_color = that.attr('status_color');

        $(this)
          .css('background', background_color)
          .css('border-left', '5px solid ' + border_color);
      },
      mouseout: function() {
        let that = $(this);

        that.css('background', 'none').css('border-left', 'none');
      }
    });
  }
};
