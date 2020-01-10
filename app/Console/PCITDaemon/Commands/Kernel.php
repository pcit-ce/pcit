<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon\Commands;

use App\Console\PCITDaemon\Migrate;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Kernel extends Command
{
    /**
     * @var \App\Console\PCITDaemon\Kernel
     */
    protected $handler;

    protected function configure(): void
    {
        $this->setName('up');

        $this->setDescription('Run PCIT Daemon');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // 数据库迁移 server only
        if (\in_array($this->getName(), ['server', 'up'], true)) {
            try {
                sleep(5);

                Migrate::all();
            } catch (Exception $e) {
                sleep(30);
                Migrate::all();
            }

            \PCIT\Framework\Support\DB::close();
        }

        \Log::info('Start Memory is '.memory_get_usage(), []);

        set_time_limit(0);

        // 进行系统检查

        $this->check();

        if (PHP_OS === 'Linux') {
            // http://www.laruence.com/2009/06/11/930.html

            while (1) {
                $this->process_execute();
                sleep(3);
            }

            exit;
        }

        while (1) {
            $this->handler->handle();
            unset($up);

            if (env('CI_DEBUG_MEMORY', false)) {
                \Log::debug('Now Memory is '.memory_get_usage());
            }

            sleep(3);
        }
    }

    /**
     * @throws Exception
     */
    protected function process_execute(): void
    {
        //创建子进程
        $pid = pcntl_fork();
        //子进程
        if (0 === $pid) {
            $this->handler->handle();

            if (env('CI_DEBUG_MEMORY', false)) {
                \Log::debug('Now Memory is '.memory_get_usage());
            }

            exit;
        } else {
            //主进程
            //取得子进程结束状态
            pcntl_wait($status, WUNTRACED);
            if (pcntl_wifexited($status)) {
                return;
            }
        }
    }

    private function check(): void
    {
        // GitHub App private key
        $private_key_root = base_path().'/framework/storage/private_key';
        $private_key = $private_key_root.'/private.key';
        $public_key = $private_key_root.'/public.key';

        if ((!file_exists($private_key)) or (!file_exists($public_key))) {
            echo "\n\n\nGitHub App private key not found\n\n\n";
            exit;
        }
    }
}
