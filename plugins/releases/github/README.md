# PCIT plugin -- GitHub Release

```yaml
steps:
  deploy:
    image: pcit/github-release
    if:
      status: success
      event: tag
    with:
      token: ${GITHUB_TOKEN}
      # repo: pcit-ce/pcit
      files:
      - file_name
      # overwrite: true
      # draft: true
      # prerelease: true
      # target_commitish: refs/tags/nightly
      # note: note
      # title: title
```
