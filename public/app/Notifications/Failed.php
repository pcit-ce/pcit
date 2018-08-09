<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Build;
use Exception;
use KhsCI\Support\CI;

class Failed extends Passed
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        if ('github' === $this->git_type) {
            GitHubAppChecks::send(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                $this->markdown(),
                null,
                null
            );
        }
    }

    /**
     * @throws Exception
     */
    public function errored(): void
    {
        // GitHub App checks API
        if ('github' === $this->git_type) {
            GitHubAppChecks::send(
                $this->build_key_id,
                null,
                CI::GITHUB_CHECK_SUITE_STATUS_COMPLETED,
                (int) Build::getStartAt($this->build_key_id),
                (int) Build::getStopAt($this->build_key_id),
                CI::GITHUB_CHECK_SUITE_CONCLUSION_FAILURE,
                null,
                null,
                $this->markdown(),
                null,
                null
            );
        }
    }

    /**
     * @return string
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
