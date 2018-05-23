# Caches

A list of caches.

## List

This returns all the caches for a repository.

> 返回仓库的构建缓存列表

| Method | URL                                              |
| :----- | :----------------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/caches` |

**Example:** `GET` `/repo/github_app/khs1994-php/khsci/caches`

## Delete

This deletes all caches for a repository.

> 删除仓库的所有构建缓存

| Method    | URL                                              |
| :-----    | :----------------------------------------------- |
| `DELETE`  | `/repo/{git_type}/{username}/{repo.name}/caches` |

**Example:** `DELETE` `/repo/github_app/khs1994-php/khsci/caches`

