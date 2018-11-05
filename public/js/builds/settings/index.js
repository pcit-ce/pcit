'use strict';

function display(data, url, token) {
  let display_element = $('#display');

  display_element.empty();

  let setting_is_default = data.length === 0;
  let setting_el = $('<div class="setting"></div>');
  let general_el = $('<form class="general"></form>');
  let auto_cancellation = $('<form class="auto_cancellation"></form>');
  let env_el = $('<form class="env"></form>');
  let cron_el = $('<form class="cron"></form>');

  general_el
    .append(() => {
      let div_el = $('<label class="setting_title"></label>');

      return div_el.append('General');
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="build_pushes"/></label>'
      );

      return input_el.append('Build Push Event');
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="build_pull_requests"/></label>'
      );

      return input_el.append('Build Pull request Event');
    })

    .append(() => {
      let input_el = $(
        '<label><input type="text" name="maximum_number_of_builds"/></label>'
      );

      return input_el.prepend('Maximum number of builds');
    });

  auto_cancellation
    .append(() => {
      return $('<label class="setting_title"></label>').append(
        'Auto Cancellation'
      );
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="auto_cancel_branch_builds"/></label>'
      );

      return input_el.append('Auto cancel push');
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="auto_cancel_pull_request_builds"/></label>'
      );

      return input_el.append('Auto cancel pull request');
    });

  env_el
    .append(() => {
      return $('<label class="setting_title"></label>').append(
        'Environment Variables'
      );
    })
    .append(() => {});

  cron_el
    .append(() => {
      return $('<label class="setting_title"></label>').append('Cron Jobs');
    })
    .append(() => {});

  setting_el.append(general_el, auto_cancellation, env_el, cron_el);

  display_element.append(setting_el);

  if (setting_is_default) {
    $(
      '.setting [name="build_pushes"],' +
        '.setting [name="build_pull_requests"]'
    )
      .prop('checked', true)
      .attr('value', '1');
  } else {
    // 遍历设置
    $.each(data, (key, value) => {
      $(`.setting [name=${key}]`)
        .prop('checked', value === '1')
        .attr('value', value === '1' ? '1' : '0');
    });
  }
}

module.exports = {
  handle: (url, token) => {
    console.log(location.href);
    $.ajax({
      type: 'get',
      url: '/api/repo/' + url.getRepoFullName() + '/settings',
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      },
      success: function(data) {
        display(data, url, token);
      }
    });
  }
};
