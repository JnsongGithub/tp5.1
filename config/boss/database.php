<?php
return [
    //Mongo配置文件
    'mongo_db' => [
        // 数据库类型
        'type'            => '\think\mongo\Connection',
        // 服务器地址
        'hostname'        => '192.168.100.102',
        // 数据库名
        'database'        => 'taoai_api',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => 'J4y48t8KsN6ThZ6vGUo0TtBM',
        // 端口
        'hostport'        => '3717',
        // 查询类型
        'query'           => '\think\mongo\Query',
    ],


    // redis缓存
    'redis_db'   =>  [
        // 驱动方式
        'type'   => 'redis',
        // 服务器地址
        'host' => '39.107.77.56',
        // redis 端口号
        'port'      => '6379',
        // redis配置的密码
        'password'  => '',
        // 缓存时间
        'timeout'   => 3600,
        //选择缓存库
        'select' => '0',
        // 缓存前缀
        'prefix' => '',
        //是否长连接
        'persistent' => false,
        //序列化
        'serialize' => '',
        'expire' => 0,
    ],

];
