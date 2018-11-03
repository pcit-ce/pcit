'use strict';

const git = require('../../common/git');
const status = require('../../common/status');
const details = require('../log/details');
const list = require('../jobs/list');
const log = require('../log');

module.exports = {
  show: (data, username, repo) => {
    console.log(data);

    let {jobs} = data;
    data.status = data.build_status;

    details.show(data, username, repo);

    if (jobs.length === 1) {
      data.id = data.build_id;
      log.show(jobs.build_log);

      return;
    }

    list.show(data, username, repo);
  }
};
