# PCIT Plugins

一个 PCIT 插件就是一个 Docker 镜像。

* 与 `GitHub Actions` 兼容，即 PCIT 插件可直接用于 `GitHub Actions`

插件通过环境变量 `INPUT_*` 来定义全部功能。

## 编写插件

开发者可以编写以下（但不限于）类型的插件

* **通知类** 微信通知、钉钉通知、邮件通知
* **存储类** S3(Minio)、对象存储、七牛云、又拍云、SFTP
* **Pages类** GitHub pages、Gitee Pages
* **Releases** GitHub Release、Gitee Releases
* **Docker** 构建 Docker 镜像
* **包管理类** NPM、PYPI、RUBYGEM
* **Kubernetes** Helm

## 标准化

### 存储类

* 必须使用 [Flysystem](https://github.com/thephpleague/flysystem) 兼容驱动。
