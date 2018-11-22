# Requests

## List

This will return a list of requests belonging to a repository.

> 返回仓库的所有 Webhooks 列表

| Method | URL                                                |
| :----- | :------------------------------------------------- |
| `GET`  | `/repo/{username}/{repo.name}/requests` |

**Example:** `GET` `/repo/pcit-ce/pcit/requests`

## Create

This will create a request for an individual repository, triggering a build to run on PCIT.

> 模拟发起一个 Webhooks

```bash
$ curl -X POST \
    -H "Content-Type: application/json" \
    -H "PCIT-API-Version: 3" \
    -H "Authorization: token xxxxxxxxxxxx" \
    -d '{ "request": {
        "message": "Override the commit message: this is an api request", "branch": "master" }}'\
    https://ci.khs1994.com/api/repo/pcit-ce/pcit/requests
```

| Method  | URL                                                |
| :-----  | :------------------------------------------------- |
| `POST`  | `/repo/{username}/{repo.name}/requests` |

| Accepted Parameter | Type     | Description                                       |
| :----------------- | :------- | ------------------------------------------------- |
| `request.config`   | `String` | Build configuration (as parsed from .pcit.yml).  |
| `request.message`  | `String` | Travis-ci status message attached to the request. |
| `request.branch`   | `String` | Branch requested to be built.                     |

## Get

This will return information about an individual request.

> 返回某个 Webhooks 的详情

| Method | URL                                                            |
| :----- | :------------------------------------------------------------- |
| `GET`  | `/repo/{username}/{repo.name}/request/{request.id}` |

**Example** `GET` `/repo/pcit-ce/pcit/request/666`
