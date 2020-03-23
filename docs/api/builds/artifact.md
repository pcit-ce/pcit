# artifact

## 获取某个仓库的资源

This returns a single repo artifact.

> 获取某个仓库的资源列表

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `{git_type}/{username}/{repo_name}/artifacts` |

## 列出某个 job 的资源

This returns a single job artifact.

> 获取某个 job 的资源列表

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `{git_type}/{username}/{repo_name}/job/{job.id}/artifacts` |

## 获取某个资源详情

This returns a single job single artifact.

> 获取某个 job 的某个资源的基本信息

| Method | URL                                      |
| :----- | :----------------------                  |
| `GET`  | `{git_type}/{username}/{repo_name}/job/{job.id}/artifacts/{artifact_name}` |

## 下载某个资源

This returns a single job single artifact.

> 下载某个 job 的某个资源

| Method | URL                                      |
| :----- | :----------------------                  |
| `GET`  | `{git_type}/{username}/{repo_name}/job/{job.id}/artifacts/{artifact_name}/{format}` |

## 删除某个资源

Delete a single job single artifact.

> 删除某个 job 的某个资源

| Method | URL                                      |
| :----- | :----------------------                  |
| `DELETE`  | `{git_type}/{username}/{repo_name}/job/{job.id}/artifacts/{artifact_name}` |
