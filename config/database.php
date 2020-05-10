<?php

/**
 * 储存库的配置
 */
return [

	//main database
	'default' => [
		'db_host'	 => getenv('DB_HOST', 'localhost'), 	//地址
		'db_port'	 => getenv('DB_PORT', '3306'),        	//端口
		'db_name'	 => getenv('DB_DATABASE', 'om_blog'),  	//数据库名称
		'db_user'	 => getenv('DB_USERNAME', 'root'),     	//用户
		'db_pass'	 => getenv('DB_PASSWORD', ''),  		//密码
		'db_charset' => 'utf8',       						//字符集
	],
];