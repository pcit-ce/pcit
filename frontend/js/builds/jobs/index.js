const details = require('../log/details');
const log = require('../log');

function display(job_data, build_data, url) {
  let { build_log, id: job_id, build_id } = job_data;

  job_data.log = build_log;
  job_data.commit_id = build_data.commit_id;
  job_data.commit_message = build_data.commit_message;
  job_data.committer_name = build_data.committer_name;
  job_data.status = job_data.state;
  job_data.branch = build_data.branch;

  $('#display').empty();

  details.show(job_data, url, true);

  // display log
  log.show(job_data.log);

  // let column_el = $('#pull_requests');
}

module.exports = {
  handle: url => {
    let job_id = url.getUrlWithArray()[7];

    $.ajax({
      type: 'get',
      url: '/api/job/' + job_id,
      success: function(data) {
        let { build_id } = data;

        $.ajax({
          url: '/api/build/' + build_id,

          success: build_data => {
            display(data, build_data, url);
          },
        });
      },
    });
  },
};
