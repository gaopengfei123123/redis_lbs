<?php
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/11
 * Time: 下午12:11
 */
if (! function_exists('dd')) {
    function dd($var = null)
    {
        if (is_null($var)) die();
        $param = func_get_args();

        foreach ($param as $item) {
            dump($item);
            echo PHP_EOL;
        }
        die();
    }
}