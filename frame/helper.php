<?php

/*
 * 全局函数
 */

//是否是手机
function isMobile()
{
	if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    } 
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
        return true;
    } 
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    } 
    if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
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