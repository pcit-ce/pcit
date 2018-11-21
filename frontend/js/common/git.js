module.exports = {
  format: gittype => {
    switch (gittype) {
      case 'github':
        return 'GitHub';

      default:
        return gittype.substring(0, 1).toUpperCase() + gittype.substring(1);
    }
  },

  getUrl: (username, repo, gitType = 'github') => {
    let url;
    switch (gitType) {
      case 'github':
        url = 'https://github.com';
        break;
      case 'gitee':
        url = 'https://gitee.com';
        break;
    }

    return url;
  },

  getCommitUrl: (username, repo, commit_id, gitType = 'github') => {
    let commitUrl;

    let repo_full_name = repo ? username + '/' + repo : username;

    switch (gitType) {
      case 'github':
        commitUrl =
          'https://github.com/' + repo_full_name + '/commit/' + commit_id;
        break;

      case 'gitee':
        commitUrl = `https://gitee.com/${repo_full_name}/commit/${commit_id}`;

        break;
    }

    return commitUrl;
  },

  getPullRequestUrl: (username, repo, pull_request_id, gitType = 'github') => {
    let prUrl;

    switch (gitType) {
      case 'github':
        prUrl = `https://github.com/${username}/${repo}/pull/${pull_request_id}`;

        break;

      case 'gitee':
        prUrl = `https://gitee.com/${username}/${repo}/pulls/${pull_request_id}`;

        break;
    }

    return prUrl;
  },
};
