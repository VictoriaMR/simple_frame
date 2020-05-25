<?php

class App 
{
	private static $class = null;
	private static $instance = null;
	const VERSION = '1.1.0';
	private static $autoload = [];

	/**
     * 版本号
     */
    public function version()
    {
        return static::VERSION;
    }

	/**
	 * 框架初始化方法 运行方法的实例化 路由解析等
	 */
	public static function run() 
	{
		/**
		 * 初始化实例
		 */
		if (!(self::$instance instanceof self)) {
			self::$instance = new self();
			self::$autoload = $GLOBALS['autoload'] ?? [];
		}

		//初始化方法
		self::init();

		return self::$instance;
	}

	/**
	 * @method 执行方法
	 * @return object_array
	 */
	public function send() 
	{
		//路由解析
		$info = Router::analyze_func();
		$class = 'App\\Http\\Controllers\\'.$info['Class'].'\\'.$info['ClassPath'].'Controller';

		App\Http\Middleware\VerifyToken::handle($info);

		call_user_func_array([self::autoload($class), $info['Func']], Router::analyze_params());
	}

	/**
	 * 初始化方法
	 */
	private static function init() 
	{
		// 自动装载 # 注册__autoload()函数
		spl_autoload_register([ __CLASS__ , 'autoload']);
	}

	protected static function autoload($abstract) 
    {
    	if (!empty(self::$autoload[$abstract]))
    		$abstract = self::$autoload[$abstract];

    	if (strtolower(substr($abstract, 0, 3)) != 'app') {
    		$fileName = 'frame/'.$abstract;
    	} else {
    		$fileName = $abstract;
    	}

        $abstractfile = ROOT_PATH . str_replace(['\\', 'App/'], ['/', 'app/'], $fileName) . '.php';

        if (is_file($abstractfile)){
			require_once $abstractfile;
		} else {
			throw new Exception( $abstractfile .' 不存在!');
		}

        $concrete = Container::getInstance()->autoload($abstract);

		return $concrete;
    }

    public static function make($abstract)
    {
    	return self::autoload($abstract);
    }	
}