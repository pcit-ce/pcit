<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Build;
use App\Job;
use Exception;
use KhsCI\Support\CI;

class Passed
{
    protected static $header = <<<EOF
# About KhsCI

**China First Support GitHub Checks API CI/CD System Powered By Docker and Tencent AI**

**Author** @khs1994

* [GitHub App](https://github.com/apps/khsci)

* [Official Website](https://ci.khs1994.com)

* [Support Documents](https://github.com/khs1994-php/khsci/tree/master/docs)

* [Community Support](https://github.com/khs1994-php/khsci/issues)

# Try KhsCI ?

Please See [KhsCI Support Docs](https://github.com/khs1994-php/khsci/tree/master/docs)

EOF;

    public $build_key_id;

    public $config;

    public $language;

    public $os;

    public $build_log;

    public $git_type;

    /**
     * Passed constructor.
     *
     * @param int         $build_key_id
     * @param string      $config
     * @param string      $language
     * @param null|string $build_log
     * @param null|string $os
     * @param string      $git_type
     *
     * @throws Exception
     */
    public function __construct(int $build_key_id,
                                string $config,
                                string $build_log = null,
                                string $language = null,
                                string $os = null,
                                $git_type = 'github')
    {
        $this->build_key_id = $build_key_id;

        $this->config = $config ??
            'This repo not include .khsci.yml file, please see https://docs.ci.khs1994.com/usage/';

        $this->language = $language ?? 'PHP';

        $this->os = $os ?? PHP_OS;

        $this->build_log = Job::getLog((int) $this->build_key_id) ??
            'This repo not include .khsci.yml file, please see https://docs.ci.khs1994.com/usage/';

        $this->git_type = $git_type;
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        if (!('github' === $this->git_type)) {
            return;
        }

        $build_key_id = $this->build_key_id;

        GitHubAppChecks::send(
            $build_key_id,
            null,
            CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
            (int) Build::getStartAt($build_key_id),
            (int) Build::getStopAt($build_key_id),
            CI::GITHUB_CHECK_SUITE_CONCLUSION_SUCCESS,
            null,
            null,
            $this->markdown(),
            null,
            null);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function markdown()
    {
        return self::$header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $this->language  |
| Operating System | $this->os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$this->config
```

</details>

# Build Log

```bash
$this->build_log
```

EOF;
    }
}
