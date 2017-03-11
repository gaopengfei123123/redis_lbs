<?php
namespace LBS\Services;
use LBS\Contracts\LBSInterface;

/**
 * 新建redis-lbs服务
 */
class LBSService implements LBSInterface
{
    protected static $redis = null;

    protected $geoset_name = 'LBS_set';

    protected $unit_allow = ['m','km','ft','mi'];
    public $radium_option = [
        'WITHDIST' => true,
        'SORT' => 'asc',
        'WITHHASH' => false,
    ];

    /**
     * 初始化，目前可选 geoset_name 和 radirm_option 参数
     * LBSService constructor.
     * @param array $option
     */
    public function __construct($option=[])
    {
        if (is_null(self::$redis)){
            $redis = new RedisServer();
            self::$redis = $redis::$server;
        }

        extract($option);
        if (isset($geoset_name) && !empty($geoset_name)){
            $this->geoset_name = $geoset_name;
        }

        if(isset($option) && !empty($option)){
            $this->radium_option = array_merge($this->radium_option,$option);
        }

    }


    /**
     * 在集合中新加一个坐标
     * @param array $params
     * @param null $key
     * @return null|string
     */
    public function add(array $params,$key = null)
    {
        $key = $key? : $this->geoset_name;

        $this->paramsFormat($params,$key);


        array_unshift($params,'GEOADD');

        $res = self::$redis->executeRaw($params);

        return $res;
    }

    /**
     * 重组添加命令，可以批量添加
     * @param $params
     * @return array
     */
    protected function paramsFormat(&$params,&$key)
    {
        if(is_array(current($params))){
            $res = array_filter(array_map([$this,'buildAddParams'],$params));
        }else{
            $res = $this->buildAddParams($params)?: [];
        }
        $params = $res;


        if(!empty($params)){
            foreach ($params as &$item){
                $item = implode('|',$item);
            }
            $params = implode('|',$params);
            $params = explode('|',$params);
            array_unshift($params,$key);

        }

        return $params;
    }

    /**
     * 单条内容的筛分
     * @param $item
     * @return array|null
     */
    protected function buildAddParams($item)
    {
        $arr = [];
        if(isset($item['long']) && isset($item['long']) && isset($item['name'])){
            $arr['long'] = $item['long'];
            $arr['lat'] = $item['lat'];
            $arr['name'] = $item['name'];
        }

        return empty($arr)? null : $arr;
    }

    /**
     * 查询范围内元素，如果不转 key就用默认的
     * @param $long
     * @param $lat
     * @param $radius
     * @param $unit
     * @param null $key
     * @return mixed
     */
    public function search($long, $lat, $radius, $unit,$key=null)
    {
        $key = is_null($key)? $this->geoset_name : $key;
        $radius = (int)$radius;
        $unit = (in_array($unit,$this->unit_allow))? $unit : 'm';
        $options = $this->radium_option;

        $res = self::$redis->georadius($key,$long,$lat,$radius,$unit,$options);

        return $this->withKey($res,$options);
    }

    public function searchByMembers($name, $radius, $unit,$key=null)
    {
        $key = is_null($key)? $this->geoset_name : $key;
        $radius = (int)$radius;
        $unit = (in_array($unit,$this->unit_allow))? $unit : 'm';
        $options = $this->radium_option;

        $res = self::$redis->georadiusbymember($key,$name,$radius,$unit,$options);

        return $this->withKey($res,$options);
    }

    public function geoEncode($long, $lat)
    {
        return self::$redis->executeRaw(['GEOENCODE',$long,$lat]);
    }

    public function geoDecode($hash)
    {
        return self::$redis->executeRaw(['GEODECODE',$hash]);
    }

    public function list($key, $start = 0, $end = -1)
    {
        $test = self::$redis->zrange($key,$start,$end);
        return $test;
    }

    /**
     * 将各种值标上对应变量
     * @param $array
     * @param $option
     * @return mixed
     */
    public function withKey(&$array,$option){
        if(isset($option['SORT'])){
            unset($option['SORT']);
        }
        foreach($array as &$item){
            $arr = [];
            if(isset($item[0])){
                $arr['name'] = $item[0];
            }else{
                $arr = null;
                continue;
            }
            if(isset($item[1])){
                $arr['dist'] = $item[1];
            }

            if(isset($item[2])){
                $arr['hash'] = $item[2];
            }
            $item = $arr;
        }

        return $array;

    }
}

