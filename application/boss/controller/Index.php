<?php
namespace app\boss\controller;

use app\common\Redis;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $redis = new Redis();
        $resu = $redis->get('name');
        return $resu;
    }


	public function test()
    {

    }
}
