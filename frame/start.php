<?php

//加载 composer 配置文件
if (is_file(ROOT_PATH . 'vendor/autoload.php')) {
	require_once ROOT_PATH . 'vendor/autoload.php';
} 

//助手 函数文件
if (is_file(ROOT_PATH . 'frame/helper.php')) {
	require_once ROOT_PATH . 'frame/helper.php';
}

//配置文件
if (is_file(ROOT_PATH . 'frame/env.php')) {
	require_once ROOT_PATH . 'frame/env.php';
}

//配置数据文件
if (is_file(ROOT_PATH . 'frame/config.php')) {
	require_once ROOT_PATH . 'frame/config.php';
}

//框架 APP 文件
require_once ROOT_PATH . 'frame/app.php';

//也可以在 index.php 定义一些自己的变量 | 设置
header("Access-Control-Allow-Origin: *");
header("Content-Root: " . Env('APP_DOMAIN'));
@session_start();

//执行文件入口
App::run()->send();

