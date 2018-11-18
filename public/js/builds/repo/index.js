'use strict';

const git = require('../../common/git');

/*
fetch

vue

no jquery
*/

function display(repos_data) {
  $('.repo_item').append(`
<div class="col-12 col-sm-12 col-md-1" hidden>{{ repo.rid }}</div>
<div class="col-12 col-sm-12 col-md-3">
<a v-bind:href="'/'+repo.git_type+'/'+repo.repo_full_name" >
{{ repo.repo_full_name }}</a>
</div>
<div class="col-12 col-sm-12 col-md-1" hidden>{{ repo.webhooks_status }}</div>
<div class="col-12 col-sm-12 col-md-2" v-if="repo.webhooks_status && repo.commit_id !=='0'">
<div class="title">DEFAULT BRANCH</div>
<a v-bind:href="'/'+repo.git_type+'/'+repo.repo_full_name+'/builds/'+repo.build_id"
:class="repo.build_status" class="message">
{{ repo.default_branch }}
</a>
</div>
<div class="col-12 col-sm-12 col-md-9" v-else-if="repo.webhooks_status">
<div class="title">Oops</div>
<div>There is no build on the default branch yet.</div>
</div>
<div class="col-12 col-sm-12 col-md-9" v-else>
<div class="title">Oops</div>
<div>This is not an active repository.</div>
</div>
<div class="col-12 col-sm-12 col-md-2" v-if="repo.webhooks_status && repo.commit_id !=='0'">
<div class="title">COMMIT</div>
<a target="_blank" 
:href="git.getCommitUrl(repo.repo_full_name,null,repo.commit_id,repo.git_type)"
:title="'View commit on '+git.format(repo.git_type)">
{{ repo.commit_id.slice(0,6) }}
</a>
</div>
<div class="col-12 col-sm-12 col-md-2" v-if="repo.webhooks_status && repo.commit_id !=='0'">
<div class="title">LAST BUILD</div>
<a 
v-bind:href="'/'+repo.git_type+'/'+repo.repo_full_name+'/builds/'+repo.build_id"
:class="repo.build_status" class="message">
# {{ repo.build_id }}
</a>
</div>
<div class="col-12 col-sm-12 col-md-3" v-if="repo.webhooks_status && repo.commit_id !=='0'">
<div class="title">Finished</div>
<div>{{ repo.build_status }} 2 days ago </div>
</div>
<div class="col-12 col-sm-12 col-md-1" hidden>{{ repo.git_type }}</div>
    `);

  new Vue({
    el: '#repos_display',
    data: {
      repos_data,
      git,
    },
    methods: {},
    computed: {},
  });
}

function display_username(username_data, git_type = 'github') {
  $('#username_display').append(`
<img :src="pic" class="username_img"/><a class="username_text" :href="git_url()">{{username}}</a>
  `);

  username_data = username_data[0];

  console.log(username_data);

  new Vue({
    el: '#username_display',
    data: {
      pic: username_data.pic,
      username: username_data.username,
    },
    methods: {
      git_url: function() {
        return git.getUrl(this.username, git_type);
      },
    },
  });
}

function request(git_type, username, token) {
  (async () => {
    let username_data = await fetch(
      '/api/user/' + [git_type, username].join('/'),
    )
      .then(res => res.json())
      .then(res => res);

    display_username(username_data);

    let username_repo_data = await fetch(
      '/api/repos/' + [git_type, username].join('/'),
    )
      .then(res => res.json())
      .then(res => res);

    display(username_repo_data, git_type);
  })();
}

module.exports = {
  handle(git_type, username, token) {
    $('#display').remove();
    $('#repo').remove();
    $('.column').remove();
    $('br').remove();
    $('#repos_display').prop('hidden', false);
    $('#username_display').prop('hidden', false);
    request(git_type, username, token);
  },
};
