# clone

`clone` 指令用来设置 Git clone。

```yaml
clone:
  git:
    image: pcit/git
    depth: 2
    recursive: true
    skip_verify: false
    tags: false
    submodule_override:
      hello-world: https://github.com/octocat/hello-world.git
    hosts:
      - "domain.com:192.168.199.100"  
```

* `submodule_override` 指令可以重写 Git 子模块地址，应用场景就是将子模块的 ssh 地址替换为 https 地址。

* `depth` 默认为 `25`，这样做的目的是加快 `clone` 速度，你也可以自行修改为合适的值。 
