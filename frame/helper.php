<?php

/*
 * 全局函数
 */

//是否是手机
function isMobile()
{
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) return true;

	//此条摘自TPM智能切换模板引擎，适合TPM开发
	if (isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT']) return true;

	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], 'wap')) return true;

	//判断手机发送的客户端标志,兼容性有待提高
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$clientKeywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'];

		//从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientKeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) return true;
	}

	//协议法，因为有可能不准确，放到最后判断 如果只支持wml并且不支持html那一定是移动设备 如果支持wml和html但是wml在html之前则是移动设备
	if (isset($_SERVER['HTTP_ACCEPT'])) {
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) 
			&& (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false 
			|| (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}

	return false;
}

//数据库函数
function DB($db = null)
{
	return DB::getInstance($db);
}

/*
 * 视图助手函数 display
 */
function view($template = '')
{
	return View::getInstance()->display($template);
}

/*
 * 视图助手函数 fetch
 */
function fetch($template = '')
{
	return View::getInstance()->fetch($template);
}

/*
 * 视图助手函数 assign
 */
function assign($name, $value = null)
{
	return View::getInstance()->assign($name, $value);
}

/*
 * 跳转助手函数 home.index.login
 */
function go($func = '')
{
	$func = explode('.', $func);

	Router::reload($func);

	App::instance()->send();
}

/**
 * 是否运行在命令行下
 * @return bool
 */
function runningInConsole()
{
    return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
}