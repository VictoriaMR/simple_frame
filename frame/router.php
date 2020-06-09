<?php

class Router
{	
	public static $_route = []; //路由
	public static $_param = []; //参数

	/**
     * @method 解析网址 解析路由 返回控制器执行路径 
     *         可自己定义控制器路径
     * @return array
     */
    public static function analyze_func()
	{
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
        $ClassPath = $pathInfoArr;

        $funcArr = [
			'Class'     => !empty($Class) ? $Class : 'Home',
			'ClassPath' => !empty($ClassPath) ? $ClassPath : 'Index',
			'Func'      => !empty($Func) ? $Func : 'index',
		];

		self::$_route = self::realFunc($funcArr);
	}

	public static function getFunc()
	{
		return self::$_route;
	}

	public static function realFunc($funcArr)
	{
		if (!empty($funcArr)) {
			$count = count($funcArr);
			$i = 1;
			foreach ($funcArr as $key => $value) {
				if (empty($value)) continue;
				if ($count == $i) {
					//方法名小写
					$funcArr[$key] = strtolower(substr($value, 0, 1)) . substr($value, 1);
				} else {
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							$value[$k] = strtoupper(substr($v, 0, 1)) . substr($v, 1);
						}
						$funcArr[$key] = implode('\\', $value);
					} else {
						$funcArr[$key] = strtoupper(substr($value, 0, 1)) . substr($value, 1);
					}
					$i ++;
				}
			}
		}

		return $funcArr;
	}

	public static function reload($funcArr = [])
	{
		if (empty($funcArr))
			return false;

		$funcArr = self::realFunc($funcArr);

		switch (count($funcArr)) {
			case 1:
				self::$_route['Func'] = $funcArr[0] ?? 'index';
				break;
			case 2:
				self::$_route['Func'] = $funcArr[1] ?? 'index';
				self::$_route['ClassPath'] = $funcArr[0] ?? 'Index';
				break;
			default:
				self::$_route['Class'] = array_shift($funcArr);
                self::$_route['Func'] = array_pop($funcArr);
                self::$_route['ClassPath'] = implode('\\', $funcArr);
				break;
		}

		return true;
	}
}