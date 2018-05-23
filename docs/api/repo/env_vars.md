# Env vars

## List

This returns a list of environment variables for an individual repository. 

> 返回仓库的构建变量列表

| Method | URL                                                |
| :----- | :------------------------------------------------  |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/env_vars` |

**Example:** `GET` `/repo/github_app/khs1994-php/khsci/env_vars`

## Create

This creates an environment variable for an individual repository.

> 新建一个仓库的构建变量

```bash
$ curl -X POST \
    -H "Content-Type: application/json" \
    -H "Travis-API-Version: 3" \
    -H "Authorization: token xxxxxxxxxxxx" \
    -d '{ "env_var.name": "FOO", "env_var.value": "bar", "env_var.public": false }' \
    https://ci.khs1994.com/api/repo/github_app/khs1994-php/khsci/env_vars
```

| Method   | URL                                                            |
| :-----   | :------------------------------------------------------------- |
| `POST`   | `/repo/{git_type}/{username}/{repo.name}/env_vars` |

| Accepted Parameter | Type      | Description                                                          |
| :----------------- | :-------- | -------------------------------------------------------------------- |
| `env_var.name`     | `String`  | The environment variable name, e.g. FOO.                             |
| `env_var.value`    | `String`  | The environment variable's value, e.g. bar.                          |
| `env_var.public`   | `Boolean` | Whether this environment variable should be publicly visible or not. |

## Find

This returns a single environment variable.

> 返回仓库的某个构建变量的信息

| Method | URL                                                            |
| :----- | :------------------------------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}` |

**Example:** `GET` `/repo/github_app/khs1994-php/khsci/env_var/666`

## Update

This updates a single environment variable.

> 更新仓库的构建变量

```bash
$ curl -X PATCH \
    -H "Content-Type: application/json" \
    -H "Travis-API-Version: 3" \
    -H "Authorization: token xxxxxxxxxxxx" \
    -d '{ "env_var.value": "bar", "env_var.public": false }' \
    https://ci.khs1994.com/api/repo/github_app/khs1994-php/khsci/{env_var.id}
```

| Method   | URL                                                            |
| :-----   | :------------------------------------------------------------- |
| `PATCH`  | `/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}` |

| Accepted Parameter | Type      | Description                                                          |
| :----------------- | :-------- | -------------------------------------------------------------------- |
| `env_var.name`     | `String`  | The environment variable name, e.g. FOO.                             |
| `env_var.value`    | `String`  | The environment variable's value, e.g. bar.                          |
| `env_var.public`   | `Boolean` | Whether this environment variable should be publicly visible or not. |

## Delete

This deletes a single environment variable.

> 删除仓库的构建变量

| Method    | URL                                                            |
| :-----    | :------------------------------------------------------------- |
| `DELETE`  | `/repo/{git_type}/{username}/{repo.name}/env_var/{env_var.id}` |

**Example:** `DELETE` `/repo/github_app/khs1994-php/khsci/env_var/666`
