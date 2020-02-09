<?php

declare(strict_types=1);

namespace PCIT\Provider\AliyunDockerRegistry;

use App\Notifications\GitHubChecksConclusion\Passed;

class CheckRunText extends Passed
{
    public $image_details;

    public function __construct(int $build_key_id, string $image_details, string $git_type = 'github')
    {
        parent::__construct($build_key_id, $git_type);

        $this->image_details = $image_details;
    }

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
<summary><strong>Aliyun Docker Registry Details</strong></summary>

```json
$this->image_details
```

</details>

EOF;
    }
}
