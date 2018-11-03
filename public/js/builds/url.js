'use strict';

let title = require('./title');

function getUrl() {
  return location.href;
}

function getHost() {
  // https://ci2.khs1994.com:10000
  return location.origin;
}

function getUrlWithArray() {
  return () => getUrl().split('/');
}

function getGitType() {
  return getUrlWithArray()()[3];
}

function getUsername() {
  return getUrlWithArray()()[4];
}

function getRepo() {
  return getUrlWithArray()()[5];
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
  return getUrlWithArray()()[7];
}

function getType() {
  let type_from_url = getUrlWithArray()()[6];

  if (6 === getUrlWithArray()().length) {
    type_from_url = 'current';
  }

  return type_from_url;
}

module.exports = {
  getUrl: getUrl(),
  getUrlWithArray: getUrlWithArray(),
  getHost: getHost(),
  getGitType: getGitType(),
  getUsername: getUsername(),
  getRepo: getRepo(),
  getRepoFullName: getRepoFullName(),
  getGitRepoFullName: getGitRepoFullName(),
  getRepoFullNameUrl: getRepoFullNameUrl(),
  getType: getType(),
  baseTitle: title.base(getGitType(), getUsername(), getRepo()),
  getJobId: getJobId(),
};
