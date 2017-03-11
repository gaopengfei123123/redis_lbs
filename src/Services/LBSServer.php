<?php
namespace LBS\Services;
use LBS\Contracts\LBSInterface;

/**
 * 新建redis-lbs服务
 */
class LBSService implements LBSInterface
{
  
    const REDIS_DATABASE = '123';

    public function add(string $key=123, array $params=[])
    {
        // TODO: Implement add() method.

        return $key;
    }

    public function search($key, $long, $lat, $radius, $unit)
    {
        // TODO: Implement search() method.
    }

    public function searchByMembers($key, $name, $radius, $unit)
    {
        // TODO: Implement searchByMembers() method.
    }

    public function geoEncode($long, $lat)
    {
        // TODO: Implement geoEncode() method.
    }

    public function geoDecode($hash)
    {
        // TODO: Implement geoDecode() method.
    }

}
