<?php 

namespace frame;

class Html
{
	public static $_CSS = [];
	public static $_JS = [];

	public static function addCss($name = '')
	{
		if (empty($name)) return false;

		$name = getenv('APP_DOMAIN') . 'css/' . $name . '.css';

		self::$_CSS[] = $name;
	}

	public static function addJs($name = '')
	{
		if (empty($name)) return false;

		$name = getenv('APP_DOMAIN') . 'js/' . $name . '.js';

		self::$_JS[] = $name;
	}

	public static function getCss()
	{
		if (empty(self::$_CSS)) return [];

		return array_unique(self::$_CSS);
	}

	public static function getJs()
	{
		if (empty(self::$_JS)) return [];

		return array_unique(self::$_JS);
	}
}