<?php

declare(strict_types=1);

namespace PCIT\Tests\Service\Build\Events;

use PCIT\Service\Build\Events\Notifications;
use PCIT\Support\Cache;
use PCIT\Tests\PCITTestCase;
use Symfony\Component\Yaml\Yaml;

class NotificationTest extends PCITTestCase
{
    public $yaml;

    public $notifications;

    /**
     * @throws \Exception
     */
    public function common(): void
    {
        $array = Yaml::parse($this->yaml);

        $json = json_encode($array);

        $notification = new Notifications(1, json_decode($json)->notifications);

        $notification->handle();

        $this->notifications = Cache::store()->hGet('pcit/1/notifications', 'email');
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
