# job

## List

This returns a list of jobs a current user has access to.

| Method | URL     |
| :----- | :------ |
| `GET`  | `/jobs` |

## Find

This returns a single job.

| Method | URL                     |
| :----- | :---------------------- |
| `GET`  | `/job/{job.id}` |

## Cancel

This cancels a currently running job.

| Method | URL                     |
| :----- | :---------------------- |
| `POST`  | `/job/{job.id}/cancel` |

## Restart

This restarts a job that has completed or been canceled.

| Method | URL                     |
| :----- | :---------------------- |
| `POST`  | `/job/{job.id}/restart` |
