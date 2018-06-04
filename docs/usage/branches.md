# branches

你可以通过 `branches` 指令配置分支构建策略。

```yaml
branches:
  include: [ master ]
  exclude: de*
  # include: [ master, release/* ]
  # exclude: [ release/1.0.0, release/1.1.* ]
```

## 常用配置

只允许 `master` 分支构建，其他分支不构建

```yaml
branches:
  include: master
```

只允许 `master` `dev` 分支构建

```yaml
branches:
  include: [ master, dev ]
```

正则匹配

```yaml
branches:
  include: release/1.1.*
```

除了 `dev` 分支，其他分支都构建

```yaml
branches:
  exclude: dev
```
