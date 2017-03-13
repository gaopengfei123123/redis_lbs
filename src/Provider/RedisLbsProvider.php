<?php

/**
 * Created by PhpStorm.
 * User: gaopengfei
 * Date: 2017/3/13
 * Time: 下午5:21
 */

namespace LBS\Provider;

use Illuminate\Support\ServiceProvider;
use LBS\Contracts\LBSInterface;
use LBS\Services\LBSServer;

class RedisLbsProvider extends ServiceProvider
{

    /**
     * 服务提供者加是否延迟加载.
     *
     * @var bool
     */
    protected $defer = true;

    public function boot(){
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('redis_lbs.php'),
        ]);
    }

    public function register(){
        $this->app->bind(LBSInterface::class,LBSServer::class);
        $this->app->singleton('LBSServer',function(){
            return new LBSServer();
        });
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides()
    {
        return [LBSInterface::class,'LBSServer'];
    }

}