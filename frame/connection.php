<?php

class Connection
{
	private $conn = null;

	//保存类实例的私有静态成员变量
	private static $_instance = null;
	private static $_connect = null;

	//定义一个私有的构造函数，确保单例类不能通过new关键字实例化，只能被其自身实例化
	private function __construct(){}

	public function __destruct()
	{
		// if (self::$_connect)
			// self::$_connect->close();
	} 

	//定义私有的__clone()方法，确保单例类不能被复制或克隆
	private function __clone(){}

	private static function connect($host, $username, $password, $port = '3306', $charset='UTF8', $database = '')
	{
		$conn =  new \mysqli($host, $username, $password, $database, $port);
		if($conn->connect_error){
			die('Connect Error ('.self::$_instance->connect_errno.') '.self::$_instance->connect_error);
		}

		//设置字符集
		$conn->set_charset($charset);

		return $conn;
	}

	public static function getInstance($database = null) 
	{
		//检测类是否被实例化
		if (!(self::$_instance instanceof self)) {

			self::$_instance = new self();

			if (empty($GLOBALS['database'])) throw new Exception("数据库连接失败： config/database 中 没有找到相关数据库配置");

			if (empty($database)) $database = 'default';

			$host = $GLOBALS['database'][$database]['db_host'];
			$username = $GLOBALS['database'][$database]['db_user'];
			$password = $GLOBALS['database'][$database]['db_pass'];
			$port = $GLOBALS['database'][$database]['db_port'];
			$database = $GLOBALS['database'][$database]['db_name'];
			$charset = $GLOBALS['database'][$database]['db_charset'];

			self::$_connect = self::$_instance::connect($host, $username, $password, $port, $charset, $database);
		}

		return self::$_connect;
	}
}

?>