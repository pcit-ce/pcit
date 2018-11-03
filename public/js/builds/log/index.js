module.exports = {
  show: (log) => {
    if (!log) {
      log = 'Build is pending';
    }

    let pre_el = $('<pre><pre>');

    pre_el.append(log);

    let display_el = $('#display');

    display_el.append(pre_el);
  }
};
