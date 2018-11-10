'use strict';

const git = require('../common/git');
const app = require('../common/app');

function getUrl() {
  return location.href;
}

function getHost() {
  // https://ci2.khs1994.com:10000
  return location.origin;
}

function getUrlWithArray() {
  return getUrl().split('/');
}

function getGitType() {
  return getUrlWithArray()[3];
}

function getUsername() {
  return getUrlWithArray()[4];
}

function getRepo() {
  return getUrlWithArray()[5];
}

function getRepoFullName() {
  return getUsername() + '/' + getRepo();
}

function getGitRepoFullName() {
  return getGitType() + '/' + getRepoFullName();
}

function getRepoFullNameUrl() {
  return getHost() + '/' + getGitRepoFullName();
}

function getJobId() {
  return getUrlWithArray()[7];
}

function getType() {
  let type_from_url = getUrlWithArray()[6];

  if (6 === getUrlWithArray().length) {
    type_from_url = 'current';
  }

  return type_from_url;
}

module.exports = {
  getUrl: () => {
    return getUrl();
  },
  getUrlWithArray: () => {
    return getUrlWithArray();
  },
  getHost: () => {
    return getHost();
  },
  getGitType: () => {
    return getGitType();
  },
  getUsername: () => {
    return getUsername();
  },
  getRepo: () => {
    return getRepo();
  },
  getRepoFullName: () => {
    return getRepoFullName();
  },
  getGitRepoFullName: () => {
    return getGitRepoFullName();
  },
  getRepoFullNameUrl: () => {
    return getRepoFullNameUrl();
  },
  getType: () => {
    return getType();
  },
  getJobId: () => {
    return getJobId();
  },

  getBaseTitle: () => {
    return (
      git.format(getGitType()) +
      ' - ' +
      getRepoFullName() +
      ' - ' +
      app.app_name
    );
  },
};
