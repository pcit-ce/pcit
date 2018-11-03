module.exports = {
  getColor: (status) => {
    let status_color;

    if (status === 'success') {
      status_color = '#39aa56';
    } else if (status === 'in_progress' || status === 'pending') {
      status_color = '#edde3f';
    } else if (status === 'canceled') {
      status_color = '#9d9d9d';
    } else {
      status_color = '#db4545';
    }

    return status_color;
  },
};
