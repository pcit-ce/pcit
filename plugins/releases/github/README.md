# PCIT GitHub Releases plugin

```yaml
pipeline:
  deploy:
    image: pcit/github_releases
    when:
      status: success
      event: tag
    # environment:
      # - GITHUB_TOKEN=x      
    file:
      - "file_name"
```
