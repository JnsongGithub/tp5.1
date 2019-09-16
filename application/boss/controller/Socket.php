<?php

namespace app\boss\controller;

use think\Controller;
use think\Request;
use think\swoole\Server;

class Socket extends Server
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function onReceive()
    {
        //
    }


}
