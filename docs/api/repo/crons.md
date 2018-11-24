# Crons

An individual cron. There can be only one cron per branch on a repository.

## List

This returns a list of crons for an individual repository.

> 返回仓库的全部计划任务

| Method | URL                                             |
| :----- | :---------------------------------------------- |
| `GET`  | `/repo/{username}/{repo.name}/crons` |

## Find

This returns a single cron.

> 返回某个计划任务的详情

| Method | URL               |
| :----- | :---------------- |
| `GET`  | `/cron/{cron.id}` |

**Example:** `GET` `/cron/666`

## Delete

This deletes a single cron.

> 删除某个计划任务

| Method    | URL               |
| :-----    | :---------------- |
| `DELETE`  | `/cron/{cron.id}` |

**Example:** `DELETE` `/cron/666`

## FindByBranch

This returns the cron set for the specified branch for the specified repository.

> 返回仓库某分支的计划任务详情

| Method | URL                                                                 |
| :----- | :------------------------------------------------------------------ |
| `GET`  | `/repo/{username}/{repo.name}/branch/{branch.name}/cron` |

**Example:** `GET` `/repo/pcit-ce/pcit/branch/master/cron`

## CreateByBranch

This creates a cron on the specified branch for the specified repository. Content-Type MUST be set in the header and an interval for the cron MUST be specified as a parameter.

> 创建计划任务

```bash
$ curl -X POST \
    -H "Content-Type: application/json" \
    -H "PCIT-API-Version: 3" \
    -H "Authorization: token xxxxxxxxxxxx" \
    -d '{ "cron.interval": "monthly" }' \
    https://ci.khs1994.com/api/repo/pcit-ce/pcit/branch/master/cron
```

| Method    | URL                                                                 |
| :-----    | :------------------------------------------------------------------ |
| `POST`  | `/repo/{username}/{repo.name}/branch/{branch.name}/cron` |

| Accepted Parameter                     | Type    | Description                      |
| :-----------------------------------   | :------ | ------------------------------   |
| `cron.interval`                        | `String`  | `daily`, `weekly` or `monthly` |
| `cron.dont_run_if_recent_build_exists` | `Boolean` |  -                             |
