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

    OPTIONS+=" --${CMD_ARG} $(echo ${item} | base64 -d) "
  done
}

split OPT $(echo \#!a=1 | base64),$(echo b=2 | base64),$(echo type=t,dest=/path | base64)

echo $OPTIONS
