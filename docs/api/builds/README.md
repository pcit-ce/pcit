# Builds

## List

This returns a list of builds for the current user. The result is paginated. The default limit is 100.

> 当前用户名下所有仓库的构建列表

| Method | URL       |
| :----- | :-------- |
| `GET`  | `/builds` |

## Find By Repo

This returns a list of builds for an individual repository. The result is paginated. Each request will return 25 results.

> 某仓库的构建列表

| Method | URL                                              |
| :----- | :----------------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/builds` |

**Example:** `GET` `/repo/github/pcit-ce/pcit/builds`

## Find By Repo Current

> 某仓库最近完成的一次构建

| Method | URL                                                     |
| :----- | :-----------------------------------------------        |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/build/current` |

**Example:** `GET` `/repo/github/pcit-ce/pcit/build/current`

## Find

This returns a single build.

> 返回某个构建的详情

| Method | URL                 |
| :----- | :------------------ |
| `GET`  | `/build/{build.id}` |

**Example:** `GET` `/build/666`

## Cancel

This cancels a currently running build. It will set the build and associated jobs to "state": `canceled`.

> 取消某个构建

| Method  | URL                        |
| :-----  | :------------------------- |
| `POST`  | `/build/{build.id}/cancel` |

**Example:** `POST` `/build/666/cancel`

## Restart

This restarts a build that has completed or been canceled.

> 重新开始某个构建

| Method  | URL                         |
| :-----  | :-------------------------- |
| `POST`  | `/build/{build.id}/restart` |

**Example:** `POST` `/build/666/restart`

**Response**

```json
{
  "@type": "pending",
  "job": {
    "@type": "job",
    "@href": "/job/384019276",
    "@representation": "minimal",
    "id": 384019276
  },
  "state_change": "restart",
  "resource_type": "job"
}
```
