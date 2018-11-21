'use strict';

module.exports = {
  getToken: git_type => {
    return Cookies.get(git_type + '_api_token');
  },
};
