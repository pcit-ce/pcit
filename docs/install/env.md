# ENV

`public/.env.*` 文件包含了自定义 KhsCI 所需的环境变量，下面详细介绍这些变量的作用

变量值仅有两种类型 `String` 和 `Bool`

* `CI_HOST` String 部署 CI 的域名

* `CI_NAME` String CI 名称

* `CI_DEBUG` Bool 是否开启 Debug

* `CI_SESSION_DOMAIN` String SESSION domain

* `CI_CODING_CLIENT_ID` String Coding.net 应用 ID
* `CI_CODING_CLIENT_SECRET` String Coding.net 应用 Secret
* `CI_CODING_CALLBACK_URL` String Coding.net 应用 OAuth 回调地址

* `CI_GITEE_CLIENT_ID` String Gitee 应用 ID
* `CI_GITEE_CLIENT_SECRET` String Gitee 应用 Secret
* `CI_GITEE_CALLBACK_URL` String Gitee 应用回调地址

* `CI_GITHUB_CLIENT_ID` String GitHub OAuth 应用 ID
* `CI_GITHUB_CLIENT_SECRET` String GitHub OAuth 应用 Secret
* `CI_GITHUB_CALLBACK_URL` String GitHub OAuth 应用回调地址

* `CI_GITHUB_APP_NAME` String GitHub App 应用名称
* `CI_GITHUB_APP_CLIENT_ID` String GitHub App OAuth ID
* `CI_GITHUB_APP_CLIENT_SECRET` String GitHub App OAuth Secret
* `CI_GITHUB_APP_CALLBACK_URL` String GitHub App OAuth 回调地址
* `CI_GITHUB_APP_ID` String GitHub App ID
* `CI_GITHUB_APP_PRIVATE_FILE` String GitHub App 私钥文件名，需要在设置页面生成，之后放到 `public/private_key/` 目录中

* `CI_TZ` String 时区设置

* `CI_REDIS_HOST`
* `CI_REDIS_PORT`

* `CI_MYSQL_HOST`
* `CI_MYSQL_PORT`
* `CI_MYSQL_USERNAME`
* `CI_MYSQL_PASSWORD`
* `CI_MYSQL_DBNAME`

* `CI_WEBHOOKS_TOKEN` String Webhooks Secert (仅支持 GitHub)
* `CI_WEBHOOKS_DEBUG` Bool Webhooks 是否开启 Debug，若开启系统将不验证 Secret

* `CI_WECHAT_TEMPLATE_ID` String
* `CI_WECHAT_USER_OPENID` String

* `CI_DOCKER_HOST` String Docker Host

* `CI_TENCENT_AI_APPID` String Tencent AI 应用 APPID (自行到 ai.qq.com 注册)
* `CI_TENCENT_AI_APPKEY` String Tencent AI 应用 APPKEY

* `CI_GITHUB_TEST_USERNAME` String 在测试系统中使用的 GitHub 用户名及密码
* `CI_GITHUB_TEST_PASSWORD` String
