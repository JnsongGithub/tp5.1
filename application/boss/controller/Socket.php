<?php

namespace app\boss\controller;

use think\Controller;
use think\Request;
use think\swoole\Server;

class Socket extends Server
{
    protected $host = '127.0.0.1';
    protected $port = 9502;
    protected $option = [
        'worker_num'=> 4,
        'daemonize'	=> true,
        'backlog'	=> 128
    ];


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function onReceive($server, $fd, $from_id, $data)
    {
        $server->send($fd, 'Swoole: '.$data);
    }

    public function onMessage()
    {

    }


}
