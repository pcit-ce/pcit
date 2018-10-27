let title = require('./title');

let url = location.href;

let url_array = url.split('/');

let baseUrl = "https://" + location.host;

let [, , , git_type, username, repo] = url_array;

let repo_full_name = username + '/' + repo;

let git_repo_full_name = git_type + '/' + username + '/' + repo;

let repo_full_name_url = baseUrl + '/' + git_repo_full_name;

let type_from_url = url_array[6];

// let build_id;

if (6 === url_array.length) {
  type_from_url = 'current';
}

let baseTitle = title.base(git_type, username, repo);

module.exports = {
  url: url,
  url_array: url_array,
  baseUrl: baseUrl,
  git_type: git_type,
  username: username,
  repo: repo,
  repo_full_name: repo_full_name,
  git_repo_full_name: git_repo_full_name,
  repo_full_name_url: repo_full_name_url,
  type_from_url: type_from_url,
  baseTitle: baseTitle,
};
