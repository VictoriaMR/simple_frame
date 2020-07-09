<?php

class Erroring
{
	 /**
     * 注册异常处理
     * @return void
     */
    public static function register()
    {
        if (Env('APP_DEBUG')) {
			error_reporting(E_ALL & ~ E_NOTICE);
	    	// set_error_handler([__CLASS__, 'error_debug']);
	        // set_exception_handler([__CLASS__, 'exception_debug']);
			register_shutdown_function([__CLASS__, 'shutdown_debug']);
		} else {
			error_reporting(0);
			register_shutdown_function([__CLASS__, 'catch_error']);
		}
    }

    public static function error_debug($errno, $errStr, $errfile = '', $errline = '')
    {
    	$msg = sprintf('<div>[%s]</div> <div> %s </div> <div> 第 %s 行 </div> <div> 错误: %s </div><br />', date( "Y-m-d H:i:s" ), $errfile, $errline, $errStr);
    	self::error_echo($msg);
    }

    public static function exception_debug($exception)
    {
    	$msg = sprintf('<div>[%s]</div> <div> %s </div> <div> 第 %s 行 </div> <div> 错误: %s </div><br />', date( "Y-m-d H:i:s" ), $exception->getFile(), $exception->getLine(), $exception->getMessage());
    	foreach ($exception->getTrace() as $key => $value) {
    		$msg .= '<div>'.sprintf(' %s, 第 %s 行', $value['file'] ?? '', $value['line']).'</div>';
    	}
    	self::error_echo($msg);
    }

    public static function error_echo($msg = '')
	{
		echo '<div style="position: absolute; z-index: 9999;"><div style="clear:both;word-wrap: break-word;font-family: Arial;font-size: 18px;border: 2px solid #c00;border-radius: 20px;margin:0 30px;padding: 20px;background-color:#FFFFE1;font-weight:600;">';
		echo $msg;
		echo '</div></div>';
		exit();
	}

	//错误处理
	public static function shutdown_debug()
	{
		$_error = error_get_last();
		if ($_error && in_array($_error['type'], array(1, 4, 16, 64, 256, 4096, E_ALL))) {
			$route = Router::getFunc();
			$msg = '';
			$msg .= '<div>网址:'.$_SERVER['REDIRECT_URL'].'</div>';
			$msg .= '<div>入口: <strong>'.implode('/', $route).'</strong></div>';
			$msg .= '<div>子目录: <strong>'.$route['ClassPath'].'</strong></div>';
			$msg .= '<div>类: <strong>'.$route['Class'].'</strong></div>';
			$msg .= '<div>方法: <strong>'.$route['Func'].'</strong></div>';
			$msg .= '<div>参数: <strong>'. json_encode(array_merge($_GET, $_POST)) .'</strong></div>';
			$msg .= '<div style="color:#c00">'.$_error['message'].'</div>';
			$msg .= '文件: <strong>'.$_error['file'].'</strong></br>';
			$msg .= '在第: <strong>'.$_error['line'].'</strong> 行</br>';
			self::error_echo($msg);
		}
	}

	/**
	 * @method 生产线上报错机制
	 * @author Victoria
	 */
	public static function catch_error()
	{
		// echo '404';
		exit();
	}
}