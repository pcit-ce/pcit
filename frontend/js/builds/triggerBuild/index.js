'use strict';

function display(data) {
  // console.log(data);

  // 移除之前的元素
  $('#branches_list option').remove();

  // branches 列表为空，则为 master
  data = data ? data : ['master'];

  // 填充 branches 列表
  $.each(data, (index, key) => {
    $('#branches_list').append($('<option></option>').append(key));
  });

  // 展示模态窗口
  $('#trigger_build_modal').modal('show');
}

module.exports = {
  handle: url => {
    $.ajax({
      type: 'GET',
      url: '/api/repo/' + url.getGitRepoFullName() + '/branches',
      success: function(data) {
        display(data);
      },
    });
  },
};
