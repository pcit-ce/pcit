'use strict';

module.exports = {
  show: log => {
    if (!log) {
      log = 'Build log is empty';
    }

    let pre_el = $('<pre class="build_log"><pre>');

    pre_el.append(log);

    let display_el = $('#display');

    display_el.append(pre_el).innerHeight(pre_el.innerHeight() + 70);
  }
};
