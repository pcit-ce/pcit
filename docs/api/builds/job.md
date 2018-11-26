# job

## List

This returns a list of jobs a current user has access to.

> 列出某个用户的 jobs 列表

| Method | URL     |
| :----- | :------ |
| `GET`  | `/jobs` |

## Find

This returns a single job.

> 获取某个 job 的详情

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `/job/{job.id}` |

## Cancel

> 取消某个 job

This cancels a currently running job.

| Method | URL                     |
| :----- | :---------------------- |
| `POST`  | `/job/{job.id}/cancel` |

## Restart

> 重新开始某个 job

This restarts a job that has completed or been canceled.

| Method | URL                     |
| :----- | :---------------------- |
| `POST`  | `/job/{job.id}/restart` |
