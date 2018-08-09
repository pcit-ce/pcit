<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Build;
use Exception;
use KhsCI\Support\CI;

class InProgress extends Passed
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        Build::updateStartAt($this->build_key_id);
        Build::updateBuildStatus($this->build_key_id, CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS);

        if ('github' === $this->git_type) {
            GitHubAppChecks::send($this->build_key_id, null,
                CI::GITHUB_CHECK_SUITE_STATUS_IN_PROGRESS,
                time(),
                null,
                null,
                null,
                null,
                $this->markdown()
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

EOF;
    }
}
