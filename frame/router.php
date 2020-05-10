<?php

class Router
{	
	/**
     * @method 解析网址 解析成路由和参数 返回控制器执行路径 
     *         可自己定义控制器路径
     * @return array
     */
    public static function analyze_func()
	{
		/**
		 * URL
		 * 1、part/show?name=abc 
		 * 2、path/part/show/abc/?name=abc
		 * 3、en/path/part/show/abc/?name=abc
		 */
        $pathInfo = trim(str_replace('.html', '', $_SERVER['PATH_INFO'] ?? ''), '/');

		/* 对Url网址进行拆分 */
		$pathInfoArr = explode( '/', $pathInfo );

		/* 进行网址解析 */
		if (!empty($GLOBALS['route'])) {
			if (!in_array($pathInfoArr[0] ?? [], $GLOBALS['route'])) {
				//压入默认站点到路由数组
				array_unshift($pathInfoArr, $GLOBALS['route'][0]);
			}
		}

		/* 去除路由中间空格 */
		$pathInfoArr = array_map('trim', $pathInfoArr);

        /* 类名 */
        $Class 	   = array_shift($pathInfoArr);
        /* 方法名 */
        $Func 	   = array_pop($pathInfoArr);
        /* 中间路径 */
        $ClassPath = implode('/', $pathInfoArr);

        $funcArr = [
			'Class'     => !empty($Class) ? $Class : 'home',
			'ClassPath' => !empty($ClassPath) ? $ClassPath : 'index',
			'Func'      => !empty($Func) ? $Func : 'index',
		];

		return self::realFunc($funcArr);
	}

	private static function realFunc($funcArr)
	{
		if (!empty($funcArr)) {
			foreach ($funcArr as $key => $value) {
				if (empty($value)) continue;

				$funcArr[$key] = strtoupper(substr($value, 0, 1)) . substr($value, 1);
			}
		}

		return $funcArr;
	}

	public static function analyze_params()
	{
		$params = [];

		if (!empty($_SERVER['QUERY_STRING']))
		{
			/* 初始化传参数组 */
			/* 对传参字符进行拆分 */
			$queryStrArr = explode( '&', trim( $_SERVER['QUERY_STRING'], '&' ) );
			foreach( $queryStrArr as $v ) 
			{
				$tmp = explode( '=', $v );
				if (empty($tmp)) continue;
				$params[$tmp[0]] = urldecode($tmp[1] ?? '');
			}
		}

		if (!empty($_REQUEST)) {
			$params = array_merge($params, $_REQUEST);
		}

		return $params;
	}
}