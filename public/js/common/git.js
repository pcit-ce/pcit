'use strict';

module.exports = {
  format: (gittype) => {
    switch (gittype) {
      case 'github':
        return 'GitHub';

      default:
        return gittype.substring(0, 1).toUpperCase() + gittype.substring(1);
    }
  },

  getCommitUrl: (username, repo, commit_id, gitType = 'github') => {
    let commitUrl;

    switch (gitType) {

      case 'github':
        commitUrl = 'https://github.com/' + username + '/' + repo + '/commit/' + commit_id;
        break;

      case 'gitee':
        commitUrl = `https://gitee.com/${username}/${repo}/commit/${commit_id}`;

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
  }
};
