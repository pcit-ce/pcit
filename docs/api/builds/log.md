# Log

## Find

This returns a single log.

> 返回构建日志

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `/job/{job.id}/log` |

**Example:** `GET` `/job/666/log`

**Response**

```json
{
  "@type": "log",
  "@href": "/job/384019274/log",
  "@representation": "standard",
  "@permissions": {
    "read": true,
    "delete_log": false,
    "cancel": false,
    "restart": false,
    "debug": false
  },
  "id": 280811776,
  "content":"LOG Content"
}
```

## Delete

This removes the contents of a log. It gets replace with the message: `Log removed at 2017-02-13 16:00:00 UTC`.

> 删除构建日志

| Method    | URL                     |
| :-----    | :---------------------- |
| `DELETE`  | `/job/{job.id}/log` |

**Example:** `DELETE` `/job/666/log`
