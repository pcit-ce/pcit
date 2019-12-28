# PCIT GitHub Releases plugin

```yaml
pipeline:
  deploy:
    image: pcit/github_releases
    when:
      status: success
      event: tag
    settings:
      token: ${GITHUB_TOKEN}
      # username:
      # password:
      # repo: pcit-ce/pcit
      files:
      - file_name
      # overwrite: true
      # draft: true
      # prerelease: true
      # target_commitish:
      # note: note
      # title: title
```
