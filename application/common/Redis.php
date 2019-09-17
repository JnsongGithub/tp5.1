<?php

/*
 * 使用示例
 * use app\common\Redis;
 * $obj = new Redis();
   $resu = $obj->lpush('test','sdds');
 *
 * */

namespace app\common;

use think\facade\Config;

class Redis extends \think\cache\driver\Redis
{
    protected $options = [];

    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        $this->options = Config::get('database.redis_db');
        parent::__construct($this->options);
    }
}
