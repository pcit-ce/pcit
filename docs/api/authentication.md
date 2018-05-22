# Authentication

## Generated API Access Token

### FROM CLI

```bash

$ khsci login -u YOUR_NAME -p YOUR_PASSWOD

$ khsci token

Your access token is XXXXX
```

### FROM Website

Login https://ci.khs1994.com/login , Then find API Access Token from Profile.

## Access API Via Tokens

Include the token in the Authorization header of each request to https://ci.khs1994.com/api:

```bash
curl -H "Authorization: token xxxxxxxxxxxx" \
     https://ci.khs1994.com/api/user/github
```
