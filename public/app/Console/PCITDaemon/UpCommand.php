<?php

declare(strict_types=1);

namespace App\Console\PCITDaemon;

use Exception;
use PCIT\Support\Env;
use PCIT\Support\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('up');

        $this->setDescription('Run PCIT Daemon');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // 数据库迁移
        try {
            sleep(5);

            Migrate::all();
        } catch (Exception $e) {
            sleep(30);
            Migrate::all();
        }

        \PCIT\Support\DB::close();

        Log::debug(__FILE__, __LINE__, 'Start Memory is '.memory_get_usage(), [], Log::INFO);

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
            $up = new Up();
            $up->up();
            unset($up);

            if (Env::get('CI_DEBUG_MEMORY', false)) {
                Log::debug(__FILE__, __LINE__, 'Now Memory is '.memory_get_usage());
            }

            sleep(3);
        }
    }

    /**
     * @throws Exception
     */
    private function process_execute(): void
    {
        //创建子进程
        $pid = pcntl_fork();
        //子进程
        if (0 === $pid) {
            $up = new Up();
            $up->up();

            if (Env::get('CI_DEBUG_MEMORY', false)) {
                Log::debug(__FILE__, __LINE__, 'Now Memory is '.memory_get_usage());
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
        $private_key_root = base_path().'/public/storage/private_key';
        $private_key = $private_key_root.'/private.key';
        $public_key = $private_key_root.'/public.key';

        if ((!file_exists($private_key)) or (!file_exists($public_key))) {
            echo "\n\n\nGitHub App private key not found\n\n\n";
            exit;
        }
    }
}
