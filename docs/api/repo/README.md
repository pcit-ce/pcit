# Repositories

## List

This returns a list of repositories the current user has access to.

> 列出当前用户名下的仓库列表

| Method | URL       |
| :----- | :-------- |
| `GET`  | `/repos` |


## List By Owner

This returns a list of repositories an owner has access to.

> 返回某个用户(或组织)名下的所有仓库列表，不包含用户名下组织的仓库列表

| Method | URL                                  |
| :----- | :------------------------------------|
| `GET`  | `/repos/{git_type}/{username}` |

**Example:** `GET` `/repos/github/khs1994-php`

## Find

This returns an individual repository.

> 返回某仓库的详情

| Method | URL                                       |
| :----- | :---------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}` |
