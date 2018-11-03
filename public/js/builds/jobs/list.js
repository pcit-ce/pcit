const common_status = require('../../common/status');

// list builds all jobs

module.exports = {
  show: (data, username, repo) => {
    let jobs_list_el = $('<div class="jobs_list"></div>');

    let {jobs} = data;

    let git_type = location.pathname.split('/', 2)[1];
    let job_url = ['/', location.host, git_type, username, repo, 'jobs'].join('/');

    $.each(jobs, (index, job) => {

      let {id, state, env_vars = ''} = job;

      env_vars = env_vars ? env_vars : 'no matrix environment set';

      let status_color = common_status.getColor(state);
      let status_background_color = common_status.getColor(state, true);

      let a_el = $('<a class="job_list"></a>');

      a_el.append(() => {
        let div_el = $('<div class="job_id"></div>');
        div_el.append('# ' + id);
        div_el.css('color', status_color);

        return div_el;
      }).append(() => {
        let div_el = $('<div class="job_os"></div>');
        div_el.append('Linux');

        return div_el;
      }).append(() => {
        let div_el = $('<div class="job_env_vars"></div>');
        div_el.append(env_vars && env_vars.slice(0, 50))
          .attr('title', env_vars);

        return div_el;
      }).append(() => {
        let div_el = $('<div class="job_run_time"></div>');
        div_el.append('run time');

        return div_el;
      }).append(() => {
        let button_el = $('<button class="job_cancel_or_restart"/>');

        button_el.append('button')
          .attr('title', 'Restart job');

        return button_el;
      }).attr('href', job_url + '/' + id)
        .attr('id', id)
        .css('cursor', 'hand')
      ;

      $('#' + id).on('mousemove', null, null, (e) => {
        console.log('mousemove');
        e.target.id.style.backgroundColor = status_background_color;
        e.target.id.style.borderLeft = '10px solid ' + status_color;
      });

      jobs_list_el.append(a_el);
    });

    let display_el = $('#display');
    display_el.append(jobs_list_el);

    // display_el.css('height', (jobs.length * 10) + 'px');
  }
};
