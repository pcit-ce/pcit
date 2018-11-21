const git = require('../common/git');
const app = require('../common/app');

module.exports = {
  // 根据被点击的元素切换标题
  show: (baseTitle, id) => {
    let title;

    switch (id) {
      case 'pull_requests':
        title = 'Pull Requests - ' + baseTitle;
        break;
      case 'builds':
        title = 'Builds - ' + baseTitle;
        break;
      default:
        title = baseTitle;
    }

    $('title').text(title);
  },

  base: (gitType, username, repo) => {
    return (
      git.format(gitType) + ' - ' + username + '/' + repo + ' - ' + app.app_name
    );
  },
};
