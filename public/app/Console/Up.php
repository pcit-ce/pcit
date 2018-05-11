<?php

namespace App\Console;

use Exception;
use KhsCI\KhsCI;
use KhsCI\Support\Cache;
use KhsCI\Support\Log;

class Up
{
    /**
     * @throws Exception
     */
    public static function up()
    {
        while (1) {
            if (1 === Cache::connect()->get('khsci_up_status')) {
                echo "Wait sleep 10s ...\n\n";

                sleep(10);

                continue;
            }

            $status = Cache::connect()->set('khsci_up_status', 1);

            // Queue::queue();

            self::updateGitHubStatus();

            echo "Finished sleep 10s ...\n\n";

            sleep(10);
        }
    }

    /**
     * @throws Exception
     */
    private static function updateGitHubStatus(): void
    {
        $array = json_decode(Cache::connect()->rPop('github_status'), true);

        if (!$array) {
            return;
        }

        $khsci = new KhsCI();

        $status = $khsci->repo_status->create(...$array);

        var_dump($status);

        Cache::connect()->set('khsci_up_status', 0);
    }
}
