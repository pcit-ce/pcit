token = require('./token');
const url = require('../builds/url');

function buttonChange(button_text) {
  if (button_text === 'cancel') {
    return {'text': 'restart', 'title': 'Restart'}
  }

  return {'text': 'cancel', 'title': 'Cancel'}
}

module.exports = {
  getColor: (status, backgroud = false) => {
    let status_color;

    if (status === 'success') {
      status_color = backgroud ? '#deecdb' : '#39aa56';
    } else if (status === 'in_progress' || status === 'pending') {
      status_color = backgroud ? '#faf6db' : '#edde3f';
    } else if (status === 'cancelled') {
      status_color = backgroud ? '#f1f1f1' : '#9d9d9d';
    } else {
      status_color = backgroud ? '#fbe8e2' : '#db4545';
    }

    return status_color;
  },

  change: (status) => {
    switch (status) {
      case 'in_progress':
        return 'started';
      case 'pending':
        return 'created';
      case 'failure':
        return 'failed';
      case 'success':
        return 'passed';
      case 'error':
        return 'errored';
      case 'cancelled':
        return 'canceled'
    }

    return status;
  },

  'getButton': (status) => {
    if (status === 'pending' || status === 'in_progress') {
      return {'text': 'cancel', 'title': 'Cancel'}
    }

    return {'text': 'restart', 'title': 'Restart'}
  },
  'buttonChange': (button_text) => {
    return buttonChange(button_text);
  },
  'buttonClick': (that) => {
    let event = that.text();
    let type = that.attr('type'); // build or job
    let id = that.attr('event_id');

    return new Promise((resolve, reject) => {
      $.ajax({
        method: 'post',
        headers: {
          'Authorization': 'token ' + token.getToken(url.getGitType()),
        },
        url: '/api/' + [type, id, event].join('/'),
        success: () => {
          let {text: button_text, title: button_title} = buttonChange(event);

          that.text(button_text);
          that.attr('title', button_title + 'type');
          resolve();
        }
      })
    });
  }
};
