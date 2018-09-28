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
    public function __construct($config_file = null)
    {
        //连接信息
        $config = [
            'scheme' => 'tcp',
            'host'   => isset($config_file['redis_connection']['host'])? $config_file['redis_connection']['host']  :'127.0.0.1',
            'port'   => isset($config_file['redis_connection']['port'])? $config_file['redis_connection']['port']  :6379,
        ];
        //可选信息
        $options = [
            'parameters' => [
                'password' => isset($config_file['redis_connection']['password'])? $config_file['redis_connection']['password'] :null,
                'database' => isset($config_file['redis_connection']['database'])? $config_file['redis_connection']['database'] :1,
            ],
            'profile' => '3.2'
//            'prefix' => 'geo_'
        ];
        self::$server = new Client($config, $options);
    }
}
