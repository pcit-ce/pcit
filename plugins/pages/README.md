# PCIT Git Pages plugin

使用 `Pages` 插件发布 `Pages`。

```yaml
pipeline:
  pages:
    settings:
      provider: pages # 必填
      keep_history: true # 保留提交历史，默认值 false
      target_branch: gh-pages # pages 分支，默认值 gh-pages
      git_url: gitee.com/pcit-ce/pcit # 必填
      local_dir: build # pages 所在文件夹，默认值 public
      email: ${EMAIL} # 提交者 email，默认值 ci@khs1994.com
      username: khs1994 # 提交者用户名，默认值 pcit-ce
      git_token: ${GITEE_TOKEN} # 必填
    when:
      status: success      
```

## Test

```bash
$ cd project

$ docker run -it -v ${PWD}:/app --workdir=/app --env-file=/path/to/.env pcit/pages
```
