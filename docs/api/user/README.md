# User

## Get Token

> 通过 Git 用户名、密码获取 Token.

```bash
$ curl -X POST \
    -d '{"git_type":"github","username":"username","password":"MyPassword"}' \
    https://ci.khs1994.com/api/user/token
```

| Method | URL |
| :----  | --- |
| `POST` | `/user/token` |

## Find By Current

This will return information about the current user.

> 返回当前用户信息

| Method | URL     |
| :----- | :------ |
| `GET`  | `/user` |

## Find

This will return information about an individual user.

> 返回某个用户的信息

| Method | URL                           |
| :----- | :---------------------------- |
| `GET`  | `/user/{git_type}/{username}` |

**Example:** `GET` `/user/gitee/khs1994`

## Sync

This triggers a sync on a user's account with their GitHub account.

> 从 Git 同步当前用户信息

| Method | URL          |
| :----- | :------------|
| `POST` | `/user/sync` |

## Active

A list of all the builds in an "active" state, either `created` or `started`.

> 返回某用户处于 **开启构建** 状态的仓库列表

|  Method   |  URL                          |
| :-----   | :--------------------------    |
| `GET`    | `/user/{git_type}/{owner.login}/active` |

**Example:** `GET` `/user/github/khs1994/active`

### Via GitHub User Id

**Example:** `GET` `/user/github_id/639823/active`
