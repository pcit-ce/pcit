# Why PCIT

## 对比 Travis CI Drone CI

最近 `Travis CI` 弃用了 **容器** 构建环境，即所有的构建环境都运行在 **虚拟机** 中。

而 `Drone CI` 所有的构建环境都运行在 **容器** 中。

PCIT 融合了上述两者的优点，形成了自己的 `.pcit.yml` 的 CI/CD 配置文件。

* PCIT 的构建环境在 **容器** 中，启动快。

* `.pcit.yml` 拥有默认定义(根据 `language` 指令)，弥补了 Drone CI 所有步骤均需自行配置的缺憾。

* PCIT 支持 `cache` 指令，弥补了 Drone 通过插件实现缓存的繁琐配置。

* more...
