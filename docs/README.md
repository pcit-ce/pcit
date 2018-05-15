# KhsCI Documents

## 体验 Demo

## GitHub 新建仓库

## 安装 KhsCI GitHub App

在 https://github.com/apps/khsci 点击右边的 `Install` 按钮，在稍后跳转的页面中选择你新建的 **仓库**

## 体验 Issue 内容中英互译

在你的 **仓库** 新建一个 Issue (内容不能为空)，提交之后，可以看到 `khsci[bot]` 回复了这个 Issue

## 项目测试、构建

> GitHub App KhsCI Demo 暂不提供项目测试、构建

**需要自行部署，部署方法请看下一节**

### 准备 PHP 项目

* 这里以 `khs1994-docker/php-demo` 为例

```bash
$ git clone https://github.com/khs1994-docker/php-demo
```

### 编辑 `.khsci.yml` 文件

* 为降低入门难度，暂时与 `Drone CI` 模板兼容，但文件名为 `.khsci.yml`

### 推送到 GitHub

```bash
$ cd php-demo

# edit file then push it to github

$ git add .

$ git commit -m "Test KhsCI"

$ git push origin master
```

在 GitHub 点击 `commits`，点击 commit 信息后的小图标，进入到构建详情页，查看构建过程及结果

## 自己部署

KhsCI 由 PHP 后端（Webhooks Server + Daemon CLI）和 GitHub App 组成。

[Coming Soon !](https://github.com/khs1994-php/khsci/blob/master/docs/install.md)
