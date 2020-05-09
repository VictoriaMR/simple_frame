<?php

class App 
{
	private static $instance = null;

	//框架初始化方法 运行方法的实例化 路由解析等
	public static function run() 
	{
		if (!(self::$instance instanceof self)) {
			print_r(456);
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function send() 
	{
		print_r(123123);
		return self::$instance;
	}

	public function get()
	{
		print_r(789);
	}
}