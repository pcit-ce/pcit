function display(data) {
  let display_element = $("#display");

  display_element.empty();

  display_element.append('settings' + data.stringify());
}

module.exports = {
  handle: (repo_full_name, token) => {

    console.log(location.href);
    $.ajax({
      type: "get",
      url: '/api/repo/' + repo_full_name + '/settings',
      headers: {
        'Authorization': 'token ' + token
      },
      success: function (data) {
        display(data);
      }
    });
  },
};

