<?php

/**
 * 入口文件
 */

//定义项目开始时间
define('MEM0RY_START', memory_get_usage());
define('APP_START_TIME', microtime(true));

//定义项目根目录
define('ROOT_PATH', str_replace('\\', '/', realpath(dirname(__FILE__).'/../').'/'));

//加载 框架 执行文件
require_once ROOT_PATH.'frame/start.php';