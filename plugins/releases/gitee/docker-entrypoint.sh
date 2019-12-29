export DRONE_BUILD_EVENT=tag

export INPUT_REPO=${INPUT_REPO:-${PCIT_REPO_SLUG}}
export DRONE_REPO_OWNER=$(echo $INPUT_REPO | cut -d "/" -f 1)
export DRONE_REPO_NAME=$(echo $INPUT_REPO | cut -d "/" -f 2)
export DRONE_COMMIT_REF=${INPUT_TARGET_COMMITISH:-${PCIT_COMMIT}}
export PLUGIN_API_KEY=${INPUT_TOKEN:-${INPUT_API_KEY}}
# export PLUGIN_FILES=${INPUT_FILES}
export PLUGIN_OVERWRITE=${INPUT_OVERWRITE:-false}
# export PLUGIN_DRAFT=${INPUT_DRAFT:-true}
export PLUGIN_PRERELEASE=${INPUT_PRERELEASE:-true}
export PLUGIN_NOTE=${INPUT_NOTE:-''}
export PLUGIN_TITLE=${INPUT_TITLE:-''}

# export GITHUB_RELEASE_BASE_URL=https://gitee.com/api/v5/

TAG_NAME=$(echo $DRONE_COMMIT_REF | grep -q 'refs/tags/' && echo $(echo $DRONE_COMMIT_REF | cut -d '/' -f 3) || echo $DRONE_COMMIT_REF )
JSON=$(echo '{"access_token":'\"${PLUGIN_API_KEY}\"',"tag_name":'\"${TAG_NAME}\"',"name":'\"${PLUGIN_TITLE}\"',"body":'\"${PLUGIN_NOTE}\"',"prerelease":'\"${PLUGIN_PRERELEASE}\"',"target_commitish":'\"${DRONE_COMMIT_REF}\"'}' )

curl -sSL -X POST https://gitee.com/api/v5/repos/${DRONE_REPO_OWNER}/${DRONE_REPO_NAME}/releases \
--header 'Content-Type: application/json;charset=UTF-8' \
--data $JSON
