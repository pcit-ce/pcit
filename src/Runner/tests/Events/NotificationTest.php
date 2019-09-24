<?php

declare(strict_types=1);

namespace PCIT\Builder\Tests\Events;

use PCIT\Builder\Events\Notifications;
use PCIT\Framework\Support\Cache;
use PCIT\Support\CacheKey;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public $yaml;

    public $notifications;

    /**
     * @throws \Exception
     */
    public function common(): void
    {
        $result = Yaml::parse($this->yaml);

        $json = json_encode($result);

        $notification = new Notifications(1, json_decode($json)->notifications);

        $notification->handle();

        $this->notifications = Cache::store()->hGet(CacheKey::notificationsHashKey(1), 'email');
    }

    /**
     * @throws \Exception
     */
    public function test_email_array(): void
    {
        $yaml = <<<'EOF'
notifications:
  email:
    - khs1994@khs1994.com
EOF;

        $this->yaml = $yaml;

        $this->common();

        $this->assertEquals(['khs1994@khs1994.com'], json_decode($this->notifications));
    }

    /**
     * @throws \Exception
     */
    public function test_email_list(): void
    {
        $yaml = <<<'EOF'
notifications:
  email:
    recipients:
      - khs1994@khs1994.com
    on_success: never # default: change
    on_failure: always # default: always
EOF;

        $this->yaml = $yaml;

        $this->common();

        $this->assertObjectHasAttribute('recipients', json_decode($this->notifications));
    }
}
