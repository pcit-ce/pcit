# System Info

## 获取 OAuth 应用的 client_id

| Method | URL                     |
| :----- | :-----------------------|
| `GET`    | `/ci/oauth_client_id` |

**Example:** `/ci/oauth_client_id`

## 获取系统待构建任务数

| Method | URL                     |
| :----- | :-----------------------|
| `GET`    | `/ci/pending`         |

## 获取 GitHub App 安装地址

| Method | URL                                   |
| :----- | :-----------------------              |
| `GET`    | `/ci/github_app_installation/{uid}` |

## 获取 GitHub App 设置地址

| Method | URL                                    |
| :----- | :-----------------------               |
| `GET`    | `/ci/github_app_settings/{org_name}` |

## About

> 获取 PCIT 描述文件（md 格式）

| Method | URL |
| :---   | :--- |
| `GET` | `/ci/about` |

## Changelog

> 获取 PCIT `CHANGELOG` 描述文件（md 格式）

| Method | URL |
| :---   | :--- |
| `GET` | `/ci/changelog` |
