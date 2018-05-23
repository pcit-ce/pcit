# Owner

## Find

This returns an individual owner. It is possible to use the GitHub login or github_id in the request.

| Method | URL                    |
| :----- | :--------------------- |
| `GET`  | `/owner/{owner.login}` |

**Example:** `GET` `/owner/khs1994`

## Active

A list of all the builds in an "active" state, either `created` or `started`.

> 返回处于 **开启构建** 状态的仓库的列表

|  Method   |  URL                          |
| :-----   | :--------------------------    |
| `GET`    | `/owner/{owner.login}/active` |

**Example:** `GET` `/owner/khs1994/active`

### Via GitHub User Id

**Example:** `GET` `/owner/github_id/639823/active`

