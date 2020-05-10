<?php 

//配置文件
require_once ROOT_PATH . 'frame/env.php';

//定义一些常量参数
define('APP_DEBUG', getenv('APP_DEBUG') ? true : false);

//配置数据文件
require_once ROOT_PATH . 'frame/config.php';

//助手 函数文件
require_once ROOT_PATH . 'frame/helper.php';

//路由文件
// require_once ROOT_PATH . 'frame/router.php';

require_once ROOT_PATH . 'frame/container.php';

//框架 APP 文件
require_once ROOT_PATH . 'frame/app.php'; //App

//执行文件入口
App::run()->send();


dd(microtime(true) - APP_START_TIME);
dd(isMobile());