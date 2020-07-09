<?php

namespace frame;

class Redis
{
	private static $_instance = null;
    private static $_link = null;
    const DEFAULT_EXT_TIME = 60;
    const DEFAULT_CONNECT_TIME = 5;

	public static function getInstance($db = 0) 
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();

            self::$_link = new \Redis();

            self::connect();
        }

        if (!self::$_link->ping()) {
            self::connect();
        }

        self::$_link->select($db);//选择数据库

        return self::$_instance;
    }

    private static function connect() 
    {
        self::$_link->connect(Env('REDIS_HOST', '127.0.0.1'), Env('REDIS_PORT', '6379'), self::DEFAULT_CONNECT_TIME);
        if (!empty(Env('REDIS_PASSWORD'))) {
            self::$_link->auth(Env('REDIS_PASSWORD'));
        }
        return true;
    }

    public function set($key, $value, $ext=null) 
    {
        if (empty($key)) return false;
        return self::$_link->set($key, $value, (int)($ext ?? self::DEFAULT_EXT_TIME));
    }

    public function __call($func, $arg)
    {
        return self::$_link->$func(...$arg);
    }
}