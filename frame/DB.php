<?php

class DB
{
	protected static $_query = null;
	protected static $_db = null;

	function __construct($db = 'default')
	{
		self::$_query = new Query();
		self::$_query->_db = $db;
	}

	public static function connection($conn = 'default')
	{
		self::$_query->_database = $conn;
		return self::$_query;
	}

	public static function table($table = '')
	{
		self::$_query->_table = $table;
		return $this->query;
	}
}