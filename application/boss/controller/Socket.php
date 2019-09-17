<?php

namespace app\boss\controller;

use think\Controller;
use think\Request;
use think\swoole\Server;

class Socket extends Server
{	
	
	private $websocket_guid = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11'; //该值是固定的，所有websocket的该值都一样
    const MAP_FD_UID_PREFIX = 'map_fd:'; //关联记录信息
	const MAX_REDIS_TIME = 86400;
	
	private $fd; //用户连接ID
	private $user_id; //用户ID
	
	//websocket配置信息
    protected $host = 'sjnweb.cn';
    protected $port = 9508;
    protected $serverType = 'socket';
    protected $option = [
        'worker_num'=> 4,
        'max_coroutine'=> 3000,
        'daemonize'	=> false,
        'backlog'	=> 128
    ];
	


	//握手成功
	public function onConnect($server, $fd)
	{
		
		echo "onConnect:{$fd}\n";
	}
	
	//执行回调
	public function onOpen($server, $request)
	{
		//做用户ID校验
		$this->fd = $request->fd;
		
		parse_str($request->server['query_string'],$param);
		$this->user_id = $param['user_id'];
		$this->matchmaker_id = $param['matchmaker_id'];
		$this->role = $param['role'];
		
		echo json_encode($param)."\n";
		go(function() use ($server,$request){
			//初始化mysql
			$swoole_mysql = $this->initMysql();
			//查询数据
			$user_info = $swoole_mysql->query('select * from test_table;');
			echo "查询用户信息:".json_encode($user_info)."\n";
			if(!$user_info)
			{
				$server->push($request->fd,"用户不存在!");
				$close = $server->disconnect($request->fd, 1000, '用户不存在');
			}
			
			
			//初始化Redis
			$swoole_redis = $this->initRedis();
			//绑定用户ID和直播间
			$redis_bind = $this->fdBindUid($swoole_redis);
			echo "绑定直播间编号:".json_encode($redis_bind)."\n";
			if(!$redis_bind)
			{
				$server->push($request->fd,"进入直播间失败!");
				$close = $server->disconnect($request->fd, 1000, '进入直播间失败!');
			}
		});
		
		//获取连接信息
		$connections = $server->connection_info($request->fd);
		
		
		
		echo "连接成功...".json_encode($connections)."\n";
		$server->push($request->fd,"连接成功");		
	}
	
	
	
	
	public function initMysql()
	{
		$swoole_mysql = new \Swoole\Coroutine\MySQL();
		$swoole_mysql->connect([
			'host' => '127.0.0.1',
			'port' => 3306,
			'user' => 'root',
			'password' => 'sjn123',
			'database' => 'swoole_test',
		]);
		return $swoole_mysql;
	}
	
	
	
	public function initRedis()
	{
		$swoole_redis = new \Swoole\Coroutine\Redis();
		$swoole_redis->connect('127.0.0.1', 6379);
		$swoole_redis->setOptions([
			'timeout' => 2,
			'serialize' => false,
			'reconnect' => 1
		]);
		// $this->redis->auth();
		$swoole_redis->select(1);
		return $swoole_redis;
	}
	
	
	
	// fd与user_id对应  
    public function fdBindUid($swoole_redis)
    {
        return $swoole_redis->setex(
            self::MAP_FD_UID_PREFIX . $this->fd,
            24 * 3600,
            $this->user_id
        );
    }


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function onReceive($server, $fd, $from_id, $data)
    {
        $server->send($fd, 'Swoole: '.$data);
    }

    public function onMessage($server, $frame)
    {
		echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }
	

	
	
	public function onRequest($request, $response)
	{
		$response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
	}

	
	public function onClose($server, $fd)
	{
		echo "{$fd}退出直播间:".json_encode($server);
	}
	
	
	
}
