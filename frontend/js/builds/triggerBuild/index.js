function display(url) {
  // 移除之前的元素
  $('#branches_list option').remove();

  const pcit = require('@pcit/pcit-js');

  const pcit_repo = new pcit.Repo('', '');

  // 获取分支列表
  (async () => {
    let data = await pcit_repo.branches.list(
      url.getGitType(),
      url.getRepoFullName(),
    );

    // branches 列表为空，则为 master
    data = data === [] ? ['master'] : data;

    // 填充 branches 列表
    $.each(data, (index, key) => {
      $('#branches_list').append($('<option></option>').append(key));
    });

    // 展示模态窗口
    $('#trigger_build_modal').modal('show');
  })();
}

module.exports = {
  handle: url => display(url),
};
