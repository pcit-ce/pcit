# NPM Plugin

> 发布 `npm` 包。

**该插件底层由 https://github.com/drone-plugins/drone-npm 支持**

```yaml
pipeline:
  deploy:
    image: pcit/npm
    settings:
      # username: yourusername # 必填（与 api_key 二选一）
      # password: yourpassword # 必填（与 api_key 二选一）
      email: khs1994@khs1994.com # 必填
      api_key: ${NPM_TOKEN} # 必填
      # tag: next # next | latest
      # registry: https://registry.npmjs.org
      # skip_verify: false
      # fail_on_version_conflict: false
      # access: public # public | restricted
    when:
      event: ['push']
      status: success
      matrix:
        - NODE_VERSION: 11
```
