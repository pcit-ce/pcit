'use strict';

const token = require('./token');
const url = require('../builds/url');

function buttonChange(handle) {
  if (handle === 'cancel') {
    return { handle: 'restart', title: 'Restart' };
  }

  return { handle: 'cancel', title: 'Cancel' };
}

function getColor(status, backgroud) {
  let status_color;

  if (status === 'success') {
    status_color = backgroud ? '#deecdb' : '#39aa56';
  } else if (
    status === 'in_progress' ||
    status === 'pending' ||
    status === 'queued'
  ) {
    status_color = backgroud ? '#faf6db' : '#edde3f';
  } else if (status === 'cancelled') {
    status_color = backgroud ? '#f1f1f1' : '#9d9d9d';
  } else {
    status_color = backgroud ? '#fbe8e2' : '#db4545';
  }

  return status_color;
}

module.exports = {
  getColor: (status, backgroud = false) => {
    return getColor(status, backgroud);
  },

  change: status => {
    switch (status) {
      case 'in_progress':
        return 'started';
      case 'pending':
        return 'created';
      case 'queued':
        return 'created';
      case 'failure':
        return 'failed';
      case 'success':
        return 'passed';
      case 'error':
        return 'errored';
      case 'cancelled':
        return 'canceled';
    }

    return status;
  },

  getButton: status => {
    if (
      status === 'pending' ||
      status === 'in_progress' ||
      status === 'queued'
    ) {
      return { handle: 'cancel', title: 'Cancel' };
    }

    return { handle: 'restart', title: 'Restart' };
  },
  buttonChange: handle => {
    return buttonChange(handle);
  },
  buttonClick: that => {
    let handle = that.attr('handle');
    let type = that.attr('job_or_build'); // build or job
    let id = that.attr('event_id');

    return new Promise((resolve, reject) => {
      $.ajax({
        method: 'post',
        headers: {
          Authorization: 'token ' + token.getToken(url.getGitType())
        },
        url: '/api/' + [type, id, handle].join('/'),
        success: () => {
          let { handle: button_handle, title: button_title } = buttonChange(
            handle
          );

          that.attr({ handle: button_handle, title: button_title + 'type' });

          resolve();
        },
        error: e => {
          reject(e);
        }
      });
    });
  }
};
