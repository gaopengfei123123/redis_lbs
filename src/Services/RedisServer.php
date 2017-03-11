<?php
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/11
 * Time: 下午1:12
 */

namespace LBS\Services;


use Predis\Client;

class RedisServer
{
    public static $server = null;
    public function __construct()
    {
        $config = [
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ];
//        $option = ['profile' => '3.2', 'prefix' => 'geo_'];
        $options = [
            'parameters' => [
                'password' => null,
                'database' => 1,
            ],
            'profile' => '3.2'
//            'prefix' => 'geo_'
        ];
        self::$server = new Client($config,$options);
    }

}