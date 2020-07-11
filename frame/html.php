<?php 

namespace frame;

class Html
{
	public static $_CSS = [];
	public static $_JS = [];

	public static function addCss($name = '')
	{
		if (empty($name)) return false;

		if (is_array($name)) {
			foreach ($name as $value) {
				self::$_CSS[] = getenv('APP_DOMAIN') . 'css/' . $value . '.css';
			}
		} else {
			self::$_CSS[] = getenv('APP_DOMAIN') . 'css/' . $name . '.css';
		}
		return true;
	}

	public static function addJs($name = '')
	{
		if (empty($name)) return false;

		if (is_array($name)) {
			foreach ($name as $value) {
				self::$_JS[] = Env('APP_DOMAIN') . 'js/' . $value . '.js';
			}
		} else {
			self::$_JS[] = Env('APP_DOMAIN') . 'js/' . $name . '.js';
		}
		return true;
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