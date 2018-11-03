module.exports = {
  getColor: (status, backgroud = false) => {
    let status_color;

    if (status === 'success') {
      status_color = backgroud ? '#deecdb' : '#39aa56';
    } else if (status === 'in_progress' || status === 'pending') {
      status_color = backgroud ? '#faf6db' : '#edde3f';
    } else if (status === 'canceled') {
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
    }

    return status;
  }
};
