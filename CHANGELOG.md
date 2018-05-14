# CHANGELOG

## v18.06.0

### 2018/05/15

使用 `.khsci.yml` 定义一切 https://github.com/khs1994-php/khsci/issues/66

### 2018/05/14

`Tencent AI + Issue = ?`，欢迎体验 https://github.com/khs1994-php/khsci/issues/64

### 2018/05/14

提交 PR 实现自动回复

指定代码审阅者 (`reviewers`), 打标签（`label`）, 指定给某人 (`assign`), 关联项目 (`project`), 关联里程碑 (`milestone`)

自动 Merge

### 2018/05/13

数据库 **软删除** 不直接删除数据, 而是通过检查标记 `deleted_at` 来确定数据是否有效（TODO）

### 2018/05/13

提出 Issue 实现自动回复

打标签 (`label`), 指定给某人 (`assign`), 关联项目 (`project`), 关联里程碑 (`milestone`)

无用问题自动 **关闭** 加 **锁定**

超时问题（最后回复时间）自动关闭

### 2018/05/13

引入 [Tencent AI](https://github.com/khs1994-php/tencent-ai)，[讨论](https://github.com/khs1994-php/khsci/issues/61).

### 2018/05/13

KhsCI 是国内首家支持 GitHub [Checks API](https://blog.github.com/2018-05-07-introducing-checks-api/) 的 CI/CD 系统

### 2018/05/09

**2018** Only Support GitHub Apps
 
### 2018/05/08

**后台任务** 刷新用户仓库列表

### 2018/05/07

**后台任务** 刷新处于活跃状态的仓库的管理员和协作者信息

### 2018/05/06

**后台任务** 暂时一次只构建一个任务

### 2018/05/03

强制将 `tmp` 数据卷挂载到 `/tmp` 目录 

### 2018/04/29

GitHub Commit 能够展示构建状态

### 2018/04/19

**强制** 使用 HTTPS

### 2018/04/18

完成 `Webhooks` 获取、增加、删除

### 2018/04/15

后端统一返回 `JSON` 格式

### 2018/04/14

所有配置通过 `.env` 载入

### 2018/04/13

完成 OAuth 登录、基本信息获取、Git Repo 列表获取
