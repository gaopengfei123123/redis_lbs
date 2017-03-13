<?php
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/11
 * Time: 下午9:22
 */
return [
    //是否应用在laravel当中
    'is_laravel' => false,
    //使用laravel的redis版本
    'laravel_redis' => 'default',


    'geoset_name' => 'LBS_set',         //集合名
    'radium_option' => [                //搜寻附近的人的时候定义的一些参数
        'WITHDIST' => true,
        'SORT' => 'asc',
        'WITHHASH' => false,
    ],
    'redis_connection' => [
        'host'     => '127.0.0.1',      //连接地址
        'port'     => 6379,             //端口
        'database' => 1,                //库索引
        'password' => null,             //密码
    ],
];