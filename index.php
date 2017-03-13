<?php namespace LBS;
require_once __DIR__.'/vendor/autoload.php';


$test = new \LBS\Services\LBSServer();
//$res = $test->searchByMembers('yabaolu',500,'km');

//添加坐标
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
$res = $test->add($add_params);

//$res2 = $test->del('gao1');

//$list = $test->list($test->geoset_name);

//$search = $test->search('116.435182767868','39.91811857809279',500,'m');
$search2 = $test->searchByMembers('fesco',500,'m');

//$list = $test->list($test->geoset_name);

Helper::dd($search2);

//$test->add('2333',['a','b','c']);