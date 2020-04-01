# Caches

A list of caches.

## List

This returns all the caches for a repository.

> 返回仓库的构建缓存列表

| Method | URL                                              |
| :----- | :----------------------------------------------- |
| `GET`  | `/repo/{username}/{repo.name}/caches` |

**Example:** `GET` `/repo/pcit-ce/pcit/caches`

## Delete branch cache

This deletes caches for a repository branch.

> 删除仓库某个分支的构建缓存

| Method    | URL                                                 |
| :-----    | :-------------------------------------------------- |
| `DELETE`  | `/repo/{username}/{repo.name}/caches/{branch.name}` |

**Example:** `DELETE` `/repo/pcit-ce/pcit/caches/master`

## Delete

This deletes all caches for a repository.

> 删除仓库的所有构建缓存

| Method    | URL                                   |
| :-----    | :------------------------------------ |
| `DELETE`  | `/repo/{username}/{repo.name}/caches` |

**Example:** `DELETE` `/repo/pcit-ce/pcit/caches`
