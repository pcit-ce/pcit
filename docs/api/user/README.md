# User

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

