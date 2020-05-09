<?php

/**
 * 入口文件
 */

//也可以在 index.php 定义一些自己的变量 | 设置
header("Access-Control-Allow-Origin: *");
@session_start();

//定义项目开始时间
define('APP_START_TIME', microtime(true));

//定义项目根目录
define('ROOT_PATH', str_replace('\\', '/', realpath(dirname(__FILE__).'/../').'/'));

//加载 composer 配置文件
if (is_file(ROOT_PATH.'vendor/autoload.php')) {
	require_once ROOT_PATH.'vendor/autoload.php';
}

//加载 框架 执行文件
require_once ROOT_PATH.'frame/start.php';

exit('Hello World!');
