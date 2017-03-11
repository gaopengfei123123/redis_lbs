<?php
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/11
 * Time: 下午12:11
 */
function dd($var){
    $param = func_get_args();

    foreach($param as $item){
        dump($item);
        echo PHP_EOL;
    }
    die();
}