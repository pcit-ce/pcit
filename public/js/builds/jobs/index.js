'use strict';

const details = require('../log/details');
const log = require('../log');

function display(job_data, build_data, username, repo) {
  job_data.log = job_data.build_log;
  job_data.commit_id = build_data.commit_id;
  job_data.commit_message = build_data.commit_message;
  job_data.committer_name = build_data.committer_name;
  job_data.status = job_data.state;
  job_data.branch = build_data.branch;

  details.show(job_data, username, repo);

  // display log
  log.show(job_data.log);
}

module.exports = {
  handle: (username, repo, job_id) => {
    console.log('jobs');
    $.ajax({
      type: 'get',
      url: '/api/job/' + job_id,
      success: function (data) {
        let build_id = data.build_id;

        $.ajax({
          url: '/api/build/' + build_id,

          success: (build_data) => {
            display(data, build_data, username, repo);
          }
        });
      }
    });
  }
};
