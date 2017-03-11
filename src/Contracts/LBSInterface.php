<?php
namespace LBS\Contracts;
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/10
 * Time: 下午7:06
 */


interface LBSInterface
{
    /**
     * 添加地址数据到集合
     * @param string $key
     * @param array $params
     * @return mixed
     */
    public function add(array $params,$key = null);

    public function del($name,$key = null);

    /**
     * 查询指定坐标范围内的所有存在内容
     * @param $key
     * @param $long
     * @param $lat
     * @param $radius
     * @param $unit
     * @return mixed
     */
    public function search($long,$lat,$radius,$unit,$key);

    /**
     * 查询集合中某个元素的范围内元素
     * @param $key
     * @param $name
     * @param $radius
     * @param $unit
     * @return mixed
     */
    public function searchByMembers($name,$radius,$unit,$key);

    /**
     * 坐标转geohash
     * @param $long
     * @param $lat
     * @return mixed
     */
    public function geoEncode($long,$lat);

    /**
     * geohash转坐标
     * @param $hash
     * @return mixed
     */
    public function geoDecode($hash);


    /**
     * 列出集合中的值
     * @param $key
     * @param $start
     * @param $end
     * @return mixed
     */
    public function list($key, $start = 0, $end = -1);


}
