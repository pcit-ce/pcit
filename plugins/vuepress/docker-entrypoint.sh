#!/usr/bin/env sh

set -x

vuepress build ${INPUT_DIR:-docs}

if [ -f ${INPUT_DIR}/CNAME ];then
  cp ${INPUT_DIR}/CNAME ${INPUT_DIR}/.vuepress/dist/
fi

if [ -f ${INPUT_DIR}/robots.txt ];then
  cp ${INPUT_DIR}/robots.txt ${INPUT_DIR}/.vuepress/dist/
fi
