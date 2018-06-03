<?php

declare(strict_types=1);

namespace KhsCI\Service\Checks;

class MarkDown
{
    private $header = <<<EOF
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

    /**
     * @param string      $language
     * @param string      $os
     * @param null|string $config
     * @param null|string $build_log
     *
     * @return string
     */
    public function success(string $language, string $os, ?string $config, ?string $build_log)
    {
        $config = $config ?? 'This repo not include .khsci.yml file';

        $build_log = $build_log ?? 'This repo not include .khsci.yml file';

        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string      $language
     * @param string      $os
     * @param null|string $config
     * @param null|string $build_log
     *
     * @return string
     */
    public function failure(string $language, string $os, ?string $config, ?string $build_log)
    {
        $config = $config ?? 'This repo .khsci.yml file parse error';

        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string $language
     * @param string $os
     * @param string $config
     * @param string $build_log
     *
     * @return string
     */
    public function neutral(string $language, string $os, string $config, string $build_log)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string      $language
     * @param string      $os
     * @param string      $config
     * @param null|string $build_log
     *
     * @return string
     */
    public function cancelled(string $language, string $os, string $config, ?string $build_log)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string      $language
     * @param string      $os
     * @param string      $config
     * @param null|string $build_log
     *
     * @return string
     */
    public function timed_out(string $language, string $os, string $config, ?string $build_log)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string      $language
     * @param string      $os
     * @param string      $config
     * @param null|string $build_log
     *
     * @return string
     */
    public function action_required(string $language, string $os, string $config, ?string $build_log)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

# Build Log

```bash
$build_log
```

EOF;
    }

    /**
     * @param string $language
     * @param string $os
     * @param string $config
     *
     * @return string
     */
    public function queued(string $language, string $os, string $config)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

EOF;
    }

    /**
     * @param string $language
     * @param string $os
     * @param string $config
     *
     * @return string
     */
    public function in_progress(string $language, string $os, string $config)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Build Configuration</strong></summary>

```json
$config
```

</details>

EOF;
    }

    /**
     * @param string $language
     * @param string $os
     * @param string $image_details
     *
     * @return string
     */
    public function aliyunDockerRegistry(string $language, string $os, string $image_details)
    {
        return $this->header.<<<EOF

# Build Configuration

|Build Option      | Setting    |
| --               |   --       |  
| Language         | $language  |
| Operating System | $os        |

<details>
<summary><strong>Aliyun Docker Registry Details</strong></summary>

```json
$image_details
```

</details>

EOF;
    }
}
