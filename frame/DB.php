<?php

namespace frame;

class DB
{
	private static $_instance = null;
	protected static $_query = null;
	protected static $_db = null;

	public static function getInstance($db = null) 
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();

            self::$_query = Query::getInstance();
			self::$_query->_db = $db;
        }
        return self::$_instance;
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