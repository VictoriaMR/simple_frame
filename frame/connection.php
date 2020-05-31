<?php

namespace frame;

class Connection
{
	private $conn = null;

	//保存类实例的私有静态成员变量
	private static $_instance = [];
	private static $_connect = null;

	//定义一个私有的构造函数，确保单例类不能通过new关键字实例化，只能被其自身实例化
	private function __construct() {}

	public function __destruct() {}

	//定义私有的__clone()方法，确保单例类不能被复制或克隆
	private function __clone() {}

	/**
	 * @method 数据库链接实例
	 * @date   2020-05-25
	 */
	private static function connect($host, $username, $password, $port = '3306', $database = '', $charset='UTF8')
	{
		$conn =  new \mysqli($host, $username, $password, $database, $port);
		if($conn->connect_error){
			throw new \Exception('Connect Error ('.$conn->connect_errno.') '.$conn->connect_error, 1);
		}

		//设置字符集
		$conn->set_charset($charset);

		return $conn;
	}

	/**
	 * @method 数据库链接单例方法
	 * @date   2020-05-25
	 */
	public static function getInstance($db = null, $database = null) 
	{
		if ($db === null) $db = 'default';
		if ($database === null) $database = getenv('DB_DATABASE');

		if (empty(self::$_instance[$db][$database])) {

			$config = $GLOBALS['database'][$db] ?? [];
			if (empty($config))
				throw new \Exception("Connect Error： Cannot found {$db} in config/database");

			self::$_instance[$db][$database] = self::connect(
				$config['db_host'] ?? '', 
				$config['db_user'] ?? '', 
				$config['db_pass'] ?? '', 
				$config['db_port'] ?? '', 
				$config['db_name'] ?? '', 
				$config['db_charset'] ?? ''
			);
		}

		return self::$_instance[$db][$database];
	}
}