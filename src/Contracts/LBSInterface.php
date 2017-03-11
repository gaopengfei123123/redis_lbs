<?php
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/10
 * Time: 下午7:06
 */

namespace LBS\Contracts;


interface LBSInterface
{
    const REDIS_DATABASE;
    public function add(string $key,array $params);

    public function search($key,$long,$lat,$radius,$unit);

    public function searchByMembers($key,$name,$radius,$unit);

    public function geoEncode($long,$lat);

    public function geoDecode($hash);


}
