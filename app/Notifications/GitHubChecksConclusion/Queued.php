<?php

declare(strict_types=1);

namespace App\Notifications\GitHubChecksConclusion;

use App\Job;
use App\Notifications\GitHubAppChecks;
use PCIT\Support\CI;

class Queued extends Kernel
{
    /**
     * @return string
     */
    public function markdown()
    {
        return self::$header.<<<EOF

## Build Configuration

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

    public function handle(): void
    {
        if ('github' !== $this->git_type) {
            return;
        }

        $job_key_id = $this->job_key_id;

        GitHubAppChecks::send(
            $job_key_id,
            null,
            CI::GITHUB_CHECK_SUITE_STATUS_QUEUED,
            (int) Job::getStartAt($job_key_id),
            (int) Job::getFinishedAt($job_key_id),
            null,
            null,
            null,
            $this->markdown(),
            null,
            null
        );
    }
}
