<?php

namespace app\Http;

/**
 * 路由中间件 
 */
class Kernel 
{
    /**
     * The application's route middleware.
     * @var array
     */
    public static $routeMiddleware = [
        //api 接口
        'Api' => \app\Http\Middleware\VerifyToken::class,
        'Admin' => \app\Http\Middleware\VerifyToken::class,
    ];
}
