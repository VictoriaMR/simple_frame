<?php 

namespace frame;

class Session
{
	public static function login($type = 'home')
	{
		return !empty($_SESSION[$type] ?? []);
	}
}