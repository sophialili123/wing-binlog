<?php namespace Wing\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStart extends ServerBase
{
    protected function configure()
    {
        $this
            ->setName('server:start')
            ->setAliases(["start"])
            ->setDescription('服务启动')
            ->addOption("d", null, InputOption::VALUE_NONE, "守护进程")
            ->addOption("debug", null, InputOption::VALUE_NONE, "调试模式")
            ->addOption("n", null, InputOption::VALUE_REQUIRED, "进程数量", 4)
//            ->addOption("with-websocket", null, InputOption::VALUE_NONE, "启用websocket服务")
//            ->addOption("with-tcp", null, InputOption::VALUE_NONE, "启用tcp服务")
//            ->addOption("with-redis", null, InputOption::VALUE_NONE, "启用redis队列服务")

        ;


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deamon      = $input->getOption("d");
        $debug       = $input->getOption("debug");
        $workers     = $input->getOption("n");

        $worker = new \Wing\Library\Worker([
                "daemon"  => !!$deamon,
                "debug"   => !!$debug,
                "workers" => $workers
            ]);
        $worker->start();
    }
}