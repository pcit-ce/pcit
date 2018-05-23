# Log

## Find

This returns a single log.

> 返回构建日志

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `/build/{build.id}/log` |

**Example:** `GET` `/build/666/log`

## Delete

This removes the contents of a log. It gets replace with the message: `Log removed at 2017-02-13 16:00:00 UTC`.

> 删除构建日志

| Method    | URL                     |
| :-----    | :---------------------- |
| `DELETE`  | `/build/{build.id}/log` |

**Example:** `DELETE` `/build/666/log`
