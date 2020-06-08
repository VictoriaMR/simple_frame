<?php 

namespace frame;

class Session
{
	public static function login($type = 'home')
	{
		return !empty($_SESSION[$type] ?? []);
	}

	public static function set($key, $data)
	{
		if (empty($key) || empty($data)) return false;

		$_SESSION[$key] = $data;

		return true;
	}
}