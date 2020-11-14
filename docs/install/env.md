# ENV

`.env.*` 文件包含了自定义 **PCIT** 所需的环境变量，下面详细介绍这些变量的作用

变量值仅有两种类型 `String` 和 `Bool`，默认值请查看这里 <https://github.com/pcit-ce/pcit/blob/master/.env.example>

| Name                      | Type   | Description                                              |
| ------------------------- | ------ | -------------------------------------------------------- |
| `CI_HOST`                 | String | 部署 CI 的域名                                                |
| `CI_NAME`                 | String | CI 名称                                                    |
| `CI_ROOT`                 | String | CI 管理员                                                   |
| `CI_DEBUG`                | Bool   | 是否开启 Debug                                               |
| `CI_DEBUG_MEMORY`         | Bool   | 是否调试程序内存占用信息                                             |
| `CI_SESSION_DOMAIN`       | String | SESSION 域名                                               |
| `CI_CODING_HOST`          | String | Coding 企业版部署地址                                           |
| `CI_CODING_CLIENT_ID`     | String | Coding.net 应用 ID                                         |
| `CI_CODING_CLIENT_SECRET` | String | Coding.net 应用 Secret                                     |
| `CI_CODING_CALLBACK_URL`  | String | Coding.net 应用 OAuth 回调地址                                 |
| `CI_GITEE_CLIENT_ID`      | String | Gitee 应用 ID                                              |
| `CI_GITEE_CLIENT_SECRET`  | String | Gitee 应用 Secret                                          |
| `CI_GITEE_CALLBACK_URL`   | String | Gitee 应用回调地址                                             |
| `CI_GITHUB_CLIENT_ID`     | String | GitHub App 应用 ID                                       |
| `CI_GITHUB_CLIENT_SECRET` | String | GitHub App 应用 Secret                                   |
| `CI_GITHUB_CALLBACK_URL`  | String | GitHub App OAuth 应用回调地址                                      |
| `CI_GITHUB_APP_NAME`      | String | GitHub App 应用名称                                          |
| `CI_GITHUB_APP_ID`        | String | GitHub App ID                                            |
| `CI_TZ`                   | String | 时区设置                                                     |
| `CI_WEBHOOKS_TOKEN`       | String | Webhooks Secert (仅 GitHub)                               |
| `CI_WEBHOOKS_DEBUG`       | Bool   | Webhooks 是否开启 Debug，若开启系统将不验证 Webhooks Secret                     |
| `CI_WECHAT_APP_ID`        | String | 微信公众平台 APP_ID                                            |
| `CI_WECHAT_APP_SECRET`    | String | 微信公众平台 APP_SECRET，若调用已存在的微信公众平台 access_token，请自行与相关人员对接。 |
| `CI_WECHAT_TOKEN`         | String | 微信公众平台消息服务器 Token，仅支持 443 端口                             |
| `CI_WECHAT_TEMPLATE_ID`   | String | 微信公众平台 模板消息 ID                                           |
| `CI_WECHAT_USER_OPENID`   | String | 微信公众平台 用户 OPENID                                         |
| `CI_DOCKER_HOST`          | String | Docker 主机 Host                                           |
| `CI_DOCKER_TLS_VERIFY`    | Bool   | Docker 主机是否已启用 TLS                                       |
| `CI_TENCENT_AI_APPID`     | String | Tencent AI 应用 APPID (自行到 ai.qq.com 注册)                   |
| `CI_TENCENT_AI_APPKEY`    | String | Tencent AI 应用 APPKEY                                     |
| `CI_GITHUB_TEST_USERNAME` | String | 在测试时使用的 GitHub 用户名及                                   |
| `CI_GITHUB_TEST_PASSWORD` | String | 在测试时使用的 GitHub 密码                                   |
| `CI_EMAIL_HOST`           | String | 电子邮件服务器地址                                                |
| `CI_EMAIL_USERNAME`       | String |                                                          |
| `CI_EMAIL_PASSWORD`       | String |                                                          |
| `CI_EMAIL_FROM`           | String | 电子邮件地址                                                   |
| `CI_EMAIL_FROM_NAME`      | String | 电子邮件来源名称                                                 |
| `CI_REDIS_HOST`           | String | 若使用 khs1994-docker/lnmp LNMP 解决方案，以下信息无需填写               |
| `CI_REDIS_PORT`           | String |                                                          |
| `CI_MYSQL_HOST`           | String |                                                          |
| `CI_MYSQL_PORT`           | String |                                                          |
| `CI_MYSQL_USERNAME`       | String |                                                          |
| `CI_MYSQL_PASSWORD`       | String |                                                          |
| `CI_MYSQL_DATABASE`       | String |                                                          |
