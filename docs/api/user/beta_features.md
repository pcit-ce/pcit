# Beta features

## List

This will return a list of beta features available to a user.

> 返回 Beta 功能列表

| Method | URL                                 |
| :----- | :----------------------------       |
| `GET`    | `/user/{user.name}/beta_features` |

**Example:** `GET` `/user/khs1994/beta_features`

## Update

This will update a user's beta_feature.

> 开启某个 Beta 功能

```bash
$ curl -X PATCH \
    -H "Content-Type: application/json" \
    -H "PCIT-API-Version: 3" \
    -H "Authorization: token xxxxxxxxxxxx" \
    -d '{"beta_feature.enabled":true}' \
    https://ci.khs1994.com/api/user/khs1994/{beta_feature.id}
```

| Method | URL                                              |
| :----- | :----------------------------------------------- |
| `PATCH`  | `/user/{user.name}/beta_feature/{beta_feature.id}` |

**Example:** `PATCH` `/user/khs1994/beta_features/{beta_feature.id}`

## Delete

This will delete a user's beta feature.

> 关闭某个 Beta 功能

| Method | URL                                              |
| :----- | :----------------------------------------------- |
| `DELETE` | `/user/{user.name}/beta_feature/{beta_feature.id}` |

**Example:** `DELETE` `/user/khs1994/beta_features/{beta_feature.id}`
