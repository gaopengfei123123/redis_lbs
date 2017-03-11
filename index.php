<?php
require_once __DIR__.'/vendor/autoload.php';


$test = new \LBS\Services\LBSService();
//$res = $test->searchByMembers('yabaolu',500,'km');
$add_params = [
    [
        'name' => 'gao1',
        'long' => '1.2312312312',
        'lat' => '1.231232232731'
    ],
    [
        'name' => 'gao2',
        'long' => '1.2312312312',
        'lat' => '1.23123252321'
    ],
    [
        'name' => 'gao3',
        'long' => '1.2312312312',
        'lat' => '1.23123234231'
    ],

    [
        'nme' => 'gao4',
        'long' => '1.2312312312',
        'lat' => '1.23122223231'
    ],
];
$res = $test->add(  $add_params);
//$res2 = $test->search()

dd($res,123);

//$test->add('2333',['a','b','c']);