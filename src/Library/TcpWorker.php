<?php namespace Wing\Library;
use Wing\FileSystem\WDir;
use Wing\Net\Tcp;
use Wing\Net\WebSocket;
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/8/5
 * Time: 07:45
 */
class TcpWorker extends BaseWorker
{
    private $clients = [];
    private $process = [];
    public function __construct()
    {
        $dir = HOME."/cache/tcp";
        (new WDir($dir))->mkdir();
    }

    /**
     * @param Tcp $tcp
     */
    private function broadcast($tcp)
    {
//        $pid = pcntl_fork();
//        if ($pid != 0) {
//            return;
//        }
        $pid = pcntl_fork();
        if ($pid > 0) {
            foreach ($this->process as $_pid) {
                (new Signal($_pid))->kill();
                //exec("kill -9 ".$_pid);
            }
//            $this->process = [];
//            $this->process[] = $pid;


            //必须等待子进程全部退出 否则子进程全部变成僵尸进程
            $start_wait = time();
            while (1) {
                $__pid = pcntl_wait($status, WNOHANG);
                if ($__pid > 0) {
                    echo $__pid, "tcp父进程等待子进程退出\r\n";
                    foreach ($this->process as $k => $v) {
                        if ($v == $__pid) {
                            unset($this->process[$k]);
                        }
                    }
                }
                if (count($this->process) <= 0 || !$this->process) {
                    break;
                }

                if ((time() - $start_wait) > 5) {
                    echo "error : tcp等待子进程退出超时\r\n";
                }
            }

            $this->process = [];
            $this->process[] = $pid;
            echo "tcp广播子进程";
            var_dump($this->process);
            return;
        }

        set_process_title("wing php >> tcp broadcast process");
        $current_process_id = get_current_processid();
        $signal = new Signal($current_process_id);

        $run_count = 0;
        $cc        = intval(1000000/self::USLEEP);
        while (1) {
            if ($run_count%$cc == 0) {
                if ($signal->checkStopSignal()) {
                    echo $current_process_id,"tcp广播进程收到终止信息号\r\n";
                    // exec("kill -9 ".$current_process_id);
                    exit;
                }
                $run_count = 0;
            }
            //广播消息
            $path[] = HOME . "/cache/tcp/*";
            //var_dump($this->clients);
            while (count($path) != 0) {
                $v = array_shift($path);
                foreach (glob($v) as $item) {
                    if (is_file($item)) {
                        $content = file_get_contents($item);
                        //$client, $buffer, $data
                        foreach ($this->clients as $w) {
                            echo "tcp发送消息：", $content, "\r\n";
                            $tcp->send($w[0], $content, $w[1]);
                        }
                        unlink($item);
                    }
                }
            }

            $run_count++;
            usleep(self::USLEEP);
        }
    }

    public function start($daemon = fasle)
    {
        $process_id = pcntl_fork();

        if ($process_id < 0) {
            echo "fork a process fail\r\n";
            exit;
        }

        if ($process_id > 0) {
            return $process_id;
        }

        if ($daemon) {
            reset_std();
        }

        //pcntl_signal(SIGCLD, SIG_IGN);

        $tcp     = new \Wing\Net\Tcp("0.0.0.0", 9997);
        //$clients = $this->clients;

        //$is_start = false;
//        pcntl_signal(SIGALRM, function($signal) use($clients){
//            echo "时钟信号\r\n";
//            var_dump($clients);
//            pcntl_alarm(1);
//        }, true);

        $tcp->on(\Wing\Net\Tcp::ON_CONNECT, function($client, $buffer) use($tcp) {
            //var_dump(func_get_args());
            $this->clients[intval($client)] = [$buffer, $client];
            $this->broadcast($tcp);
            //$this->writeNum($clients, $tcp);
//            if (!$is_start)pcntl_alarm(1);
//            $is_start = true;
        });

        $tcp->on(\Wing\Net\Tcp::ON_RECEIVE, function($client, $buffer, $recv_msg) use($tcp){
            //var_dump(func_get_args());

//            if (0 === strpos($recv_msg, 'GET')) {
//                //  echo "收到握手消息：",($recv_msg),"\r\n\r\n";
//                //握手消息
//                $tcp->handshake($buffer, $recv_msg, $client);//, $recv_msg), $client );
//                return;
//            }

            // echo "收到的消息：",\Wing\Net\WebSocket::decode($recv_msg),"\r\n\r\n";
            //一般的消息响应
            //$tcp->send($buffer, "1239999999999", $client);

        });

        $tcp->on(Tcp::ON_CLOSE, function($client, $buffer) use($tcp){
            unset($this->clients[intval($client)]);
            $this->broadcast($tcp);
            //$this->writeNum($clients, $tcp);
        });

        $tcp->on(Tcp::ON_ERROR,function($client, $buffer, $error) use($tcp){
            unset($this->clients[intval($client)]);
            $this->broadcast($tcp);
        });
        //pcntl_alarm(1);
        $tcp->start();
        return 0;
    }
}