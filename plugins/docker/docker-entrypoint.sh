#!/usr/bin/env bash

#. ./.env.example

export DOCKER_USERNAME=${INPUT_USERNAME}
export DOCKER_PASSWORD=${INPUT_PASSWORD}

# login
set +x
echo ${DOCKER_PASSWORD} | docker login -u ${DOCKER_USERNAME} --password-stdin "${INPUT_REGISTRY}"
set -x

docker buildx rm pcit-builder || true
docker buildx create --name pcit-builder --driver docker-container --use --driver-opt image=${INPUT_BUILDX_IMAGE:-"moby/buildkit:buildx-stable-1"}

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

    if [ ${CMD_ARG} = "tag" ];then
      if [ "${INPUT_REGISTRY}" ];then
        item=${INPUT_REGISTRY}/${item}
      fi
      OPTIONS+=" --${CMD_ARG} ${item} "
      continue
    fi

    if [ ${CMD_ARG} = "cache-from" \
       -o ${CMD_ARG} = "cache-to" \
       -o ${CMD_ARG} = "secret" \
       -o ${CMD_ARG} = "output" \
       ];then
      item=$(echo $item | sed "s/%2C/,/g")
      item=$(echo $item | sed "s/%25/%/g")
    fi

    OPTIONS+=" --${CMD_ARG} ${item} "
  done
}

# build-arg
split build-arg ${INPUT_BUILD_ARGS}
# label
split label ${INPUT_LABELS}
# tag
split tag ${INPUT_REPO}
# cache-from
split cache-from ${INPUT_CACHE_FROM}
# cache-to
split cache-to ${INPUT_CACHE_TO}
# secret
split secret ${INPUT_SECRET}
# output
split output ${INPUT_OUTPUT}

docker buildx build \
--platform ${INPUT_PLATFORM:-linux/amd64} \
-f ${INPUT_DOCKERFILE:-Dockerfile} \
$(test "${INPUT_DRY_RUN}" != "true" && echo " --push ") \
$(test -n "${INPUT_TARGET}" && echo " --target=${INPUT_TARGET} " ) \
$(test "${INPUT_PULL}" = "true" && echo " --pull ") \
$(test "${INPUT_NO_CACHE}" = "true" && echo " --no-cache ") \
${OPTIONS} \
${INPUT_CONTEXT:-.}

docker buildx rm pcit-builder
