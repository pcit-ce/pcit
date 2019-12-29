# PCIT plugin -- Gitee Release

```yaml
pipeline:
  deploy:
    image: pcit/gitee_release
    when:
      status: success
      event: tag
    settings:
      token: ${GITEE_TOKEN}
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
