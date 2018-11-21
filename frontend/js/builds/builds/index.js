'use strict';

const details = require('../log/details');
const list = require('../jobs/list');
const log = require('../log');

module.exports = {
  show: (data, url) => {
    console.log(data);

    // 没有 build 数据
    if (!data) {
      log.show(null);

      return;
    }

    let { jobs, build_id, build_status } = data;

    data.status = build_status;

    details.show(data, url);

    // jobs 为空
    if (jobs.length === 0) {
      log.show(null);

      return;
    }

    // 只有一个 job 直接展示日志
    if (jobs.length === 1) {
      data.id = build_id;
      let { build_log } = jobs[0];
      log.show(build_log);

      return;
    }

    // 有多个 job ,展示 jobs 列表
    list.show(data, url);
  },
};
