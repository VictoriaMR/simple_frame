<?php


//定义一些常量参数
define('APP_DEBUG', getenv('APP_DEBUG') ? true : false);
define('IS_AJAX', false);
define('IS_MOBILE', false);
define('MEM0RY_START', memory_get_usage());

//也可以在 index.php 定义一些自己的变量 | 设置
header("Access-Control-Allow-Origin: *");
@session_start();

//加载 composer 配置文件
// if (is_file(ROOT_PATH . 'vendor/autoload.php')) {
// 	require_once ROOT_PATH . 'vendor/autoload.php';
// } 

//配置文件
if (is_file(ROOT_PATH . 'frame/env.php')) {
	require_once ROOT_PATH . 'frame/env.php';
}

//配置数据文件
if (is_file(ROOT_PATH . 'frame/config.php')) {
	require_once ROOT_PATH . 'frame/config.php';
}

//助手 函数文件
if (is_file(ROOT_PATH . 'frame/helper.php')) {
	require_once ROOT_PATH . 'frame/helper.php';
}

//框架 APP 文件
require_once ROOT_PATH . 'frame/app.php';

//执行文件入口
App::run()->send();

