# KhsCI Documents

## Install

[Coming Soon !](https://github.com/khs1994-php/khsci/blob/master/docs/install.md)

## Usage

## GitHub 新建仓库

## 安装 KhsCI GitHub App

在 https://github.com/apps/khsci 点击右边的 `Install` 按钮，在稍后跳转的页面中选择你新建的仓库

## 准备 PHP 项目

* 这里以 `khs1994-docker/php-demo` 为例

```bash
$ git clone https://github.com/khs1994-docker/php-demo
```

## 编辑 `.drone.yml` 文件

* 为降低入门难度，暂时以 `Drone CI` 的文件作为模板

## 推送到 GitHub

```bash
$ cd php-demo

# edit file then push it to github

$ git add .

$ git commit -m "Test KhsCI"

$ git push origin master
```

在 GitHub 点击 `commits`，点击 commit 信息后的小图标，进入到构建详情页，查看构建过程及结果
