<?php
namespace LBS\Facade;
/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/13
 * Time: 下午5:04
 */
class LBSServerFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LBSServer';
    }

}