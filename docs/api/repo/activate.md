# Activate

## Activate

This will activate a repository, allowing its tests to be run on PCIT.

> 激活仓库构建

| Method  | URL                                                |
| :-----  | :------------------------------------------------- |
| `POST`  | `/repo/{username}/{repo.name}/activate` |

## Deactivate

> 暂停仓库构建

This will deactivate a repository, preventing any tests from running on PCIT.

| Method  | URL                                                  |
| :-----  | :-------------------------------------------------   |
| `POST`  | `/repo/{username}/{repo.name}/deactivate` |
