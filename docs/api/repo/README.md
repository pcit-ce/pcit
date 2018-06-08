# Repositories

## List

This returns a list of repositories the current user has access to.

> 列出当前用户名下的仓库列表

| Method | URL       |
| :----- | :-------- |
| `GET`  | `/repos/` |


## List By Owner

This returns a list of repositories an owner has access to.

> 返回某个用户名下的所有仓库列表

| Method | URL                                  |
| :----- | :------------------------------------|
| `GET`  | `/{git_type}/{username}/repos` |

**Example:** `GET` `/owner/github/khs1994-php/repos`

## Find

This returns an individual repository.

> 返回某仓库的详情

| Method | URL                                       |
| :----- | :---------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}` |
