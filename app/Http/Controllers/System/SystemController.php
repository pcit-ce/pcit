<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use App\Http\Controllers\Users\JWTController;
use Exception;
use PCIT\Support\Env;

/**
 * 系统 API.
 */
class SystemController
{
    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getOAuthClientId()
    {
        $git_type = (JWTController::getUser())[0];

        switch ($git_type) {
            case 'github':
                $url = 'https://github.com/settings/connections/applications/';

                break;
        }

        $url = $url.env('CI_'.strtoupper($git_type).'_CLIENT_ID');

        return compact('url');
    }

    public function getGitHubAppSettingsUrl(string $org_name = null)
    {
        $url = 'https://github.com/settings/installations';

        if ('null' === strtolower($org_name)) {
            $org_name = null;
        }

        if ($org_name) {
            $url = "https://github.com/organizations/{$org_name}/settings/installations";
        }

        return compact('url');
    }

    public function getGitHubAppInstallationUrl($uid)
    {
        $app_name = strtolower(env('CI_GITHUB_APP_NAME'));

        $url = "https://github.com/apps/{$app_name}/installations/new/permissions?suggested_target_id=".$uid;

        return compact('url');
    }

    public function about()
    {
        $data = <<<EOF
`PCIT` 是国内首个基于 `GitHub Checks API` 使用 **PHP** 编写的开源持续集成/持续部署 (CI/CD) 系统。

在 **2018** 年主要基于 `GitHub` 进行开发。

预计将在 **2019** 年支持国内主流 Git 服务商 `gitee.com`，让国内开发者体验到现代化的 CI/CD 工具集。

开发者可以使用 `.pcit.yml` 定义 CI/CD 工作流。

```yaml
# .pcit.yml 示例文件

language: php

pipeline:

  test:
    command:
      - composer install

  script:
    commands:
      - ./vendor/bin/phpunit

  after_success:
    commands:
      - echo "Build is success"
```

更多信息在 `https://docs.ci.khs1994.com` 查看。

EOF;

        return compact('data');
    }

    public function changelog()
    {
        $data = file_get_contents(base_path().'CHANGELOG.md');

        return compact('data');
    }
}
