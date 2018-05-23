# Activate

## Activate

This will activate a repository, allowing its tests to be run on KhsCI.

| Method  | URL                                                |
| :-----  | :------------------------------------------------- |
| `POST`  | `/repo/{git_type}/{username}/{repo.name}/activate` |

## Deactivate

This will deactivate a repository, preventing any tests from running on KhsCI.

| Method  | URL                                                  |
| :-----  | :-------------------------------------------------   |
| `POST`  | `/repo/{git_type}/{username}/{repo.name}/deactivate` |
