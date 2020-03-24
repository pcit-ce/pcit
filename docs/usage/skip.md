# skip build

像大多数 CI 系统一样，只要 commit 信息中包含以下内容 (不区分大小写)，PCIT 就会跳过构建。

`[skip ci]` `[ci skip]` `[pcit skip]` `[skip pcit]`

例如：

```bash
$ git add .

$ git commit -m "Update docs [skip ci]"
```

```bash
$ git commit -m "Update news [pcit skip]"
```
