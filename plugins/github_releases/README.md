# GitHub Releases Uploading

```yaml
pipeline:
  deploy:
    image: khsci/github_releases
    when:
      status: success
      event: tag
    # environment:
      # - GITHUB_TOKEN=x      
    file:
      - "file_name"
```
