'use strict';

function get_env(url, token) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: '/api/repo/' + [url.getRepoFullName(), 'env_vars'].join('/'),
      headers: {
        Authorization: 'token ' + token.getToken(url.getGitType())
      },
      success: data => {
        resolve(data);
      }
    });
  });
}

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
      let div_el = $('<div class="setting_title"></div>');

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
        '<label><input type="text" name="maximum_number_of_builds" value="1"/></label>'
      );

      return input_el.prepend('Maximum number of builds');
    });

  auto_cancellation
    .append(() => {
      return $('<div class="setting_title"></div>').append('Auto Cancellation');
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="auto_cancel_branch_builds"/></label>'
      );

      return input_el.append('Auto cancel push builds');
    })
    .append(() => {
      let input_el = $(
        '<label><input type="radio" name="auto_cancel_pull_request_builds"/></label>'
      );

      return input_el.append('Auto cancel pull request builds');
    });

  env_el.append(() => {
    return $('<div class="setting_title"></div>').append(
      'Environment Variables'
    );
  });

  get_env(url, token).then(result => {
    display_element.innerHeight(300 + result.length * 30);

    $.each(result, (index, data) => {
      let { id, name, public: is_public, value } = data;

      let env_item_el = $('<div class="env_list_item"></div>').attr({
        env_id: id,
        public: is_public
      });

      env_item_el
        .append(() => {
          return $('<div class="env_name"></div>').append(name);
        })
        .append(() => {
          return $('<div class="env_value"></div>').append(value);
        })
        .append(() => {
          return $('<button class="delete"></button>').append('Delete');
        });

      env_el.append(env_item_el);
    });

    env_el.append(() => {
      return $('<form class="new_env"></form>')
        .append(() => {
          return $('<input class="name" type="text" value="Name"/>');
        })
        .append(() => {
          return $('<input class="value" type="text" value="Value"/>');
        })
        .append(() => {
          return $(
            '<label class="is_public"><input type="radio" name="is_public" value="0" /></label>'
          ).append('Public Value');
        })
        .append(() => {
          return $('<button></button>').append('Add');
        });
    });
  });

  cron_el
    .append(() => {
      return $('<div class="setting_title"></div>').append('Cron Jobs');
    })
    .append(() => {
      return $('<label>计划构建功能即将上线</label>');
    });

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
        .attr(
          'value',
          key === 'maximum_number_of_builds' ? value : value === '1' ? '1' : '0'
        );
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
