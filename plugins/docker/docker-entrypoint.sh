#!/usr/bin/env bash

#. ./.env.example

export DOCKER_USERNAME=${INPUT_USERNAME}
export DOCKER_PASSWORD=${INPUT_PASSWORD}

# login

set +x
echo ${DOCKER_PASSWORD} | docker login -u ${DOCKER_USERNAME} --password-stdin "${INPUT_REGISTRY}"
set -x

docker buildx create --name mybuilder --driver docker-container --use

INPUT_IMAGE=${INPUT_REPO}:${INPUT_TAGS:-latest}

# 若 INPUT_DOCKER_REGISTRY 存在，则镜像名加上地址
[ -n "${INPUT_REGISTRY}" ] && INPUT_IMAGE="${INPUT_REGISTRY}/${INPUT_IMAGE}"

split(){

  CMD_ARG=$1

  OLD_IFS="$IFS"

  #设置分隔符
  IFS=","

  #如下会自动分隔
  arr=($2)

  #恢复原来的分隔符
  IFS="$OLD_IFS"

  #遍历数组
  for item in ${arr[@]}
  do
    OPTIONS+=" --${CMD_ARG} ${item} "
  done
}

# build_args
# labels
split build-arg ${INPUT_BUILD_ARGS}
split label ${INPUT_LABELS}

docker buildx build \
-t ${INPUT_IMAGE} \
--platform ${INPUT_PLATFORM:-linux/amd64} \
-f ${INPUT_DOCKERFILE:-Dockerfile} \
$(test "${INPUT_DRY_RUN}" != "true" && echo " --push ") \
$(test -n "${INPUT_TARGET}" && echo " --target=${INPUT_TARGET} " ) \
$(test "${INPUT_PULL}" = "true" && echo " --pull ") \
$(test "${INPUT_NO_CACHE}" = "true" && echo " --no-cache ") \
${OPTIONS} \
${INPUT_CONTEXT:-.}

# docker buildx rm mybuilder
