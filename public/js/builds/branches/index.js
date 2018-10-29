const {column_span_click} = require('../common');

function display(data) {
  let display_element = $("#display");

  display_element.empty();

  if (0 === data.length) {
    display_element.append('Not Build Yet !');
  } else {
    console.log(data);
    $.each(data, function (num, branch) {

      display_element.append(branch);

      $.each(status, function (id, status) {
        id = id.replace('k', '');

        let stopped_at = status[3];

        if (null == stopped_at) {
          stopped_at = 'Pending';
        } else {
          let d;
          d = new Date(stopped_at * 1000);
          stopped_at = d.toLocaleString();
        }

        display_element.append(`<tr>
<td><a href="${main}/${id}" target='_blank'># ${id} </a></td>
<td>${nbsp}${status[0]}${nbsp}</td>
<td>${nbsp}${status[2]}${nbsp}</td>
<td>${nbsp}${stopped_at}${nbsp}</td>
<td><a href="${status[4]}" target='_black'>${status[1]}</a></td>
</tr>
`);
      });

      display_element.append("<hr>");

    })
  }
}

module.exports = {
  handle: (git_repo_full_name) => {
    column_span_click('branches');
    $.ajax({
      type: "GET",
      url: '/api/repo/' + git_repo_full_name + '/branches',
      success: function (data) {
        display(data);
      }
    });
  },
};
