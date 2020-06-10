<?php 

namespace frame;

class Session
{
	public static function login($type = 'home')
	{
		return !empty($_SESSION[$type] ?? []);
	}

	public static function set($key, $data = [])
	{
		if (empty($key)) return false;

		$_SESSION[$key] = $data;

		return true;
	}

	public static function getInfo($name = '') 
	{
		if (empty($name)) return $_SESSION;
		return $_SESSION[$name] ?? '';
	}
}