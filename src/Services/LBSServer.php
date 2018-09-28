<?php
namespace LBS\Services;

use LBS\Contracts\LBSInterface;

/**
 * 新建redis-lbs服务
 * 这里为了节省代码就把所有关于集合的值给设置了个默认的，输入顺序和redis api的有点区别
 */
class LBSServer implements LBSInterface
{
    protected static $redis = null;

    public $geoset_name = 'LBS_set';

    protected $unit_allow = ['m','km','ft','mi'];
    public $radium_option = [
        'WITHDIST' => true,
        'SORT' => 'asc',
        'WITHHASH' => false,
    ];

    /**
     * 初始化，目前可选 geoset_name 和 radirm_option 参数
     * 比如 $option = [
     *              'geoset_name' => 'xxx',
     *              'radium_option' => [
     *                      'WITHDIST' => true,
                            'SORT' => 'asc',
                            'WITHHASH' => false,
     *                  ]
     *              ]
     * LBSService constructor.
     * @param array $option
     */
    public function __construct($config = null)
    {
        $config = $this->getConfig($config);
        extract($config);

        if (is_null(self::$redis)) {
            $redis = new RedisServer($config);
            self::$redis = $redis::$server;
        }


        if (isset($geoset_name) && !empty($geoset_name)) {
            $this->geoset_name = $geoset_name;
        }

        if (isset($radium_option) && !empty($radium_option)) {
            $this->radium_option = array_merge($this->radium_option, $radium_option);
        }
    }

    /**
     * 这里是获取配置文件的方法
     * @return array
     */
    protected function getConfig($config = null)
    {
        $file =$config?: include_once(__DIR__.'/../config/config.php');
        if (function_exists('config')) {
            $file = $config?:config('redis_lbs');

            if (isset($file['is_laravel']) && $file['is_laravel'] && isset($file['laravel_redis'])) {
                $file['redis_connection'] = config("database.redis.{$file['laravel_redis']}");
            }
        }
        return $file?:[];
    }


    /**
     * 在集合中新加一个坐标
     * @param array $params
     *  结构是 ['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']或者[['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']]
     * @param null $key
     * @return int
     */
    public function add(array $params, $key = null)
    {
        $key = $key? : $this->geoset_name;

        $this->paramsFormat($params, $key);


        array_unshift($params, 'GEOADD');

        $res = self::$redis->executeRaw($params);

        return $res;
    }

    /**
     * 删除集合中指定元素
     * @param $name
     * @param null $key  默认存在集合，可以指定
     * @return int
     */
    public function del($name, $key = null)
    {
        $key = $key? : $this->geoset_name;
        return self::$redis->zrem($key, $name);
    }

    /**
     * 重组添加命令，可以批量添加
     * @param $params
     * @param $key
     * @return array
     */
    protected function paramsFormat(&$params, &$key)
    {
        if (is_array(current($params))) {
            $res = array_filter(array_map([$this,'buildAddParams'], $params));
        } else {
            $res = $this->buildAddParams($params)?: [];
        }
        $params = $res;


        if (!empty($params)) {
            foreach ($params as &$item) {
                $item = implode('|', $item);
            }
            $params = implode('|', $params);
            $params = explode('|', $params);
            array_unshift($params, $key);
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
        if (isset($item['long']) && isset($item['long']) && isset($item['name'])) {
            $arr['long'] = $item['long'];
            $arr['lat'] = $item['lat'];
            $arr['name'] = $item['name'];
        }

        return empty($arr)? null : $arr;
    }

    /**
     * 根据坐标查询范围内元素，如果不转 key就用默认的
     * @param $long     //经度
     * @param $lat      //纬度
     * @param $radius   //范围
     * @param $unit     //单位  (仅支持 m,km,ft,mi)
     * @param null $key 集合名
     * @return mixed
     */
    public function search($long, $lat, $radius, $unit, $key=null)
    {
        $key = is_null($key)? $this->geoset_name : $key;
        $radius = (float)$radius;
        $unit = (in_array($unit, $this->unit_allow))? $unit : 'm';
        $options = $this->radium_option;

        $res = self::$redis->georadius($key, $long, $lat, $radius, $unit, $options);

        return $this->withKey($res, $options);
    }

    /**
     * 根据集合中的元素查询范围内元素，如果不转 key就用默认的
     * @param $name         //集合中的元素名
     * @param $radius       //范围
     * @param $unit         //单位
     * @param null $key     //集合名
     * @return mixed
     */
    public function searchByMembers($name, $radius, $unit, $key=null)
    {
        $key = is_null($key)? $this->geoset_name : $key;
        $radius = (int)$radius;
        $unit = (in_array($unit, $this->unit_allow))? $unit : 'm';
        $options = $this->radium_option;

        $res = self::$redis->georadiusbymember($key, $name, $radius, $unit, $options);

        return $this->withKey($res, $options);
    }

    //待完善
    public function geoEncode($long, $lat)
    {
        return self::$redis->executeRaw(['GEOENCODE',$long,$lat]);
    }
    //待完善
    public function geoDecode($hash)
    {
        return self::$redis->executeRaw(['GEODECODE',$hash]);
    }

    /**
     * 列出集合中的内容
     * @param $key          //集合的key
     * @param int $start    //起始位置
     * @param int $end      //结束位置 -1 为直到末尾
     * @return array
     */
    public function list($key, $start = 0, $end = -1)
    {
        $test = self::$redis->zrange($key, $start, $end);
        return $test;
    }

    /**
     * 将各种值标上对应变量
     * @param $array
     * @param $option
     * @return mixed
     */
    public function withKey(&$array, $option)
    {
        if (isset($option['SORT'])) {
            unset($option['SORT']);
        }
        foreach ($array as &$item) {
            if (is_array($item)) {
                $arr = [];
                if (isset($item[0])) {
                    $arr['name'] = $item[0];
                } else {
                    $arr = null;
                    continue;
                }
                if (isset($item[1])) {
                    $arr['dist'] = $item[1];
                }

                if (isset($item[2])) {
                    $arr['hash'] = $item[2];
                }
                $item = $arr;
            }
        }

        return $array;
    }
}
