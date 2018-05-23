# Branches

The branch of a GitHub repository. Useful for obtaining information about the last build on a given branch.

## List

This will return a list of branches a repository has on GitHub.

> 返回仓库包含的所有分支列表

| Method | URL                                                |
| :----- | :------------------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/branches` |

**Example:** `GET` `/repo/github_app/khs1994-php/khsci/branches`

## Find

This will return information about an individual branch.

> 返回仓库某分支的信息

| Method | URL                                                            |
| :----- | :------------------------------------------------------------- |
| `GET`  | `/repo/{git_type}/{username}/{repo.name}/branch/{branch.name}` |

**Example:** `GET` `/repo/github_app/khs1994-php/khsci/branch/master`
