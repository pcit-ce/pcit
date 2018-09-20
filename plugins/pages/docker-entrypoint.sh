#!/bin/bash

set -ex

echo "Preparing deploy"

rm -rf ${local_dir}/.git

if [ ${keep_history:-true} == 'true' ];then
  git clone --bare -b ${target_branch:-gh-pages} https://${git_url} ${local_dir}/.git || echo
fi

if ! [ -d ${local_dir} ];then exit; fi

echo "Deploying application"

cd ${local_dir}

if ! [ -d .git ];then
  new=true
fi

git init

git add .

git config user.name ${name}

git config user.email ${email}

git commit -m "Deploy pages by PCIT"

set +e
git remote get-url origin && git remote rm origin
set -e

git remote add origin https://${name}:${git_token}@${git_url}.git

if [ ${new:-false} == 'true' ];then git push origin master:${target_branch} ; exit 0; fi

git push origin ${target_branch}:${target_branch}
