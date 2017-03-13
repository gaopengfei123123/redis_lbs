# 安装
需要使用composer，[安装composer](https://getcomposer.org/download/), [composer中国镜像](http://www.phpcomposer.com/)

如果是应用在项目当中的话找到根目录，需要和 `composer.json`同级

```
composer require gaopengfei/redis_lbs
```

## 配置
如果不是 `laravel` 框架的话，需要修改配置文件 `src/config/config.php`
```
  'geoset_name' => 'LBS_set',
    'radium_option' => [
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

```
***但是***，如果是在 `vendor` 文件夹下修改就不能将它从版本库中移除了，所以也可以按照以上的格式写一个数组初始化的时候添加进去比如:
```
$config = [
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
 $lbs = new \LBS\Services\LBSService($config);
```

如果是 `laravel` 框架下，需要编辑 `config/app.php`
```
 'providers' => [
    ...
     \LBS\Provider\RedisLbsProvider::class,
    ...
  ],
 
 //如果需要facade模式的话也可以开一下
  'aliases' => [
    ...
    'LBSServer' => \LBS\Facade\LBSServer::class,
    ...
  ]
```

然后执行
```
php artisan vendor:publish
```
将生成 `config/redis_lbs.php` 配置文件，配置文件中的
```
//是否应用在laravel当中
'is_laravel' => false,
//使用laravel的redis版本
'laravel_redis' => 'default',
```
当 `is_laravel => true` 的时候， `laravel_redis => 'default'` 将调用 `config/database.php`下的redis相应的配置


有以下三种使用方式
```
1> $lbs = new \LBS\Services\LBSServer();

2> public function __construct(LBSInterface $LBS)
       {
           $list = $LBS->list($LBS->geoset_name);
   
           dd($list);
       }
   }
3> $search2 = \LBSServer::searchByMembers('fesco',500,'m');

```

#基本操作

## 初始化
```
require_once __DIR__.'/vendor/autoload.php';
$lbs = new \LBS\Services\LBSService();
```

## 添加
```
$add_params = [
    [
        'name' => 'yabao_road',
        'long' => '116.43620200729366',
        'lat' => '39.916880160714435'
    ],
    [
        'name' => 'jianguomen',
        'long' => '116.4356870231628',
        'lat' => '39.908560377800676'
    ],
    [
        'name' => 'chaoyangmen',
        'long' => '116.4345336732864',
        'lat' => '39.924466658329585'
    ],
    [
        'name' => 'galaxy_soho',
        'long' => '116.4335788068771',
        'lat' => '39.921372916981106'
    ],
    [
        'name' => 'cofco',
        'long' => '116.43564410781856',
        'lat' => '39.92024564137184'
    ],
    [
        'name' => 'fesco',
        'long' => '116.435182767868',
        'lat' => '39.91811857809279'
    ],


];
/**
 * 在集合中新加一个坐标
 * @param array $params
 *  结构是 ['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']或者[['name'=>'xxx','long'=>'1.2321','lat'=>'1.3112']]
 * @param null $key
 * @return int
 */
$res = $lbs->add($add_params);

返回
int 6
```

## 删除
```
/**
 * 删除集合中指定元素
 * @param $name
 * @param null $key  默认存在集合，可以指定
 * @return int
 */
$res = $lbs->del('gao1');

返回
int 0 或 1


如果是指定的集合名就
$res = $lbs->del('gao1','set-name');
```

## 用坐标查询附近的单位
```
/**
 * 查询范围内元素，如果不转 key就用默认的
 * @param $long     经度
 * @param $lat      纬度
 * @param $radius   范围
 * @param $unit     单位  (仅支持 m,km,ft,mi)
 * @param null $key 集合名
 * @return mixed
 */
$search = $lbs->search('116.435182767868','39.91811857809279',500,'m');

返回数组
array:4 [▼
  0 => array:2 [▼
    "name" => "fesco"
    "dist" => "0.1250"
  ]
  1 => array:2 [▼
    "name" => "yabao_road"
    "dist" => "162.8454"
  ]
  2 => array:2 [▼
    "name" => "cofco"
    "dist" => "239.7758"
  ]
  3 => array:2 [▼
    "name" => "galaxy_soho"
    "dist" => "386.9165"
  ]
]
```

## 根据已有的位置查询
```
/**
 * 根据集合中的元素查询范围内元素，如果不转 key就用默认的
 * @param $name         集合中的元素名
 * @param $radius       范围
 * @param $unit         单位
 * @param null $key     集合名
 * @return mixed
 */
$search = $lbs->->searchByMembers('fesco',500,'m');

返回数组
array:4 [▼
  0 => array:2 [▼
    "name" => "fesco"
    "dist" => "0.1250"
  ]
  1 => array:2 [▼
    "name" => "yabao_road"
    "dist" => "162.8454"
  ]
  2 => array:2 [▼
    "name" => "cofco"
    "dist" => "239.7758"
  ]
  3 => array:2 [▼
    "name" => "galaxy_soho"
    "dist" => "386.9165"
  ]
]
```

## 列出集合的所有值（其实就是 zrange)
```
/**
 * 列出集合中的内容
 * @param $key          集合的key
 * @param int $start    起始位置
 * @param int $end      结束位置 -1 为直到末尾
 * @return array
 */
$list = $lbs->list($test->geoset_name,2,-1);

返回数组
array:6 [▼
  0 => "jianguomen"
  1 => "yabao_road"
  2 => "fesco"
  3 => "cofco"
  4 => "galaxy_soho"
  5 => "chaoyangmen"
]
```



