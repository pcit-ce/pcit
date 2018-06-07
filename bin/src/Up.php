<?php

declare(strict_types=1);

use KhsCI\Support\Env;
use KhsCI\Support\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Up extends Command
{
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('up');

        $this->setDescription('Run KhsCI Daemon');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            sleep(5);

            \App\Console\Migrate::all();
        } catch (Exception $e) {
            sleep(30);
            \App\Console\Migrate::all();
        }

        \KhsCI\Support\DB::close();

        Log::debug(__FILE__, __LINE__, 'Start Memory is '.memory_get_usage());

        set_time_limit(0);

        if (PHP_OS === 'Linux') {
            // http://www.laruence.com/2009/06/11/930.html

            while (1) {
                $this->process_execute();
                sleep(3);
            }

            exit;
        }

        while (1) {
            $up = new \App\Console\Up();
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
    public function process_execute(): void
    {
        //创建子进程
        $pid = pcntl_fork();
        //子进程
        if (0 == $pid) {
            $up = new \App\Console\Up();
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
}
