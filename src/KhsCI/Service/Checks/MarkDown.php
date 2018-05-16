<?php

namespace KhsCI\Service\Checks;


class MarkDown
{
    private static $header = <<<EOF
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

    public function success(string $language, string $os, string $config, string $build_log)
    {
        return self::$header.<<<EOF

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

    public function failure(string $language, string $os, string $config, ?string $build_log)
    {
        return self::$header.<<<EOF

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

    public function neutral(string $language, string $os, string $config, string $build_log)
    {
        return self::$header.<<<EOF

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

    public function cancelled(string $language, string $os, string $config, ?string $build_log)
    {
        return self::$header.<<<EOF

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

    public function timed_out(string $language, string $os, string $config, ?string $build_log)
    {
        return self::$header.<<<EOF

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

    public function action_required(string $language, string $os, string $config, ?string $build_log)
    {
        return self::$header.<<<EOF

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

    public function queued(string $language, string $os, string $config)
    {
        return self::$header.<<<EOF

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

    public function in_progress(string $language, string $os, string $config)
    {
        return self::$header.<<<EOF

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
}
