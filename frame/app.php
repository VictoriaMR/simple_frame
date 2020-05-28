<?php

class App 
{
	private static $class = null;
	private static $_instance = null;
	const VERSION = '5.1.0';
	private static $autoload = [];

	/**
     * 版本号
     */
    public function version()
    {
        return static::VERSION;
    }

    public static function instance() 
    {
    	if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}

		return self::$_instance;
    }

	/**
	 * 框架初始化方法 运行方法的实例化 路由解析等
	 */
	public static function run() 
	{
		//初始化方法
		self::init();

		//解析路由
		Router::analyze_func();

		return self::instance();
	}

	/**
	 * @method 执行方法
	 * @return object_array
	 */
	public function send() 
	{
		//路由解析
		$info = Router::$_route;

		$class = 'App\\Http\\Controllers\\'.$info['Class'].'\\'.$info['ClassPath'].'Controller';

		\App\Http\Middleware\VerifyToken::handle($info);

		call_user_func_array([self::autoload($class), $info['Func']], Router::analyze_params());

		self::debugModeInit();
	}

	/**
	 * 初始化方法
	 */
	private static function init() 
	{
		// 自动装载 # 注册__autoload()函数
		spl_autoload_register([ __CLASS__ , 'autoload']);
	}

	/**
	 * @method 自动加载
	 * @date   2020-05-25
	 * @param  $abstract 对象
	 * @return object
	 */
	protected static function autoload($abstract) 
    {
    	if (!empty(self::$autoload[$abstract])) {
    		$abstract = self::$autoload[$abstract];
    	}

    	if (strtolower(substr($abstract, 0, 3)) != 'app') {
    		$fileName = 'frame/'.$abstract;
    	} else {
    		$fileName = $abstract;
    	}

        $abstractfile = ROOT_PATH . str_replace(['\\', 'App/'], ['/', 'app/'], $fileName) . '.php';

        if (is_file($abstractfile)){
			require_once $abstractfile;
		} else {
			throw new Exception( $abstractfile .' was not exist!');
		}

        $concrete = Container::getInstance()->autoload($abstract);

		return $concrete;
    }

    /**
     * @method 实例化入口
     * @date   2020-05-25
	 * @param  $abstract 对象
	 * @return object
     */
    public static function make($abstract)
    {
    	return self::autoload($abstract);
    }

    /**
     * 调试模式设置
     * @access protected
     * @return void
     */
    protected static function debugModeInit()
    {
        // 应用调试模式
        if (!getenv('APP_DEBUG')) return false;

        // 获取基本信息
        $runtime = number_format(microtime(true) - APP_START_TIME, 10, '.', '');
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem     = number_format((memory_get_usage() - MEM0RY_START) / 1024, 2);

        $uri = implode(' ', [
        	$_SERVER['SERVER_PROTOCOL'],
        	$_SERVER['REQUEST_METHOD'],
        	$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
        ]);

        $base = [
            '请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
            '运行时间' => number_format((float) $runtime, 6) . 's [ 吞吐率：' . $reqs . 'req/s ] 内存消耗：' . $mem . 'kb 文件加载：' . count(get_included_files()),
            '查询信息' => '',
            '缓存信息' => '',
        ];

        $info = get_included_files();

        foreach ($info as $key => $file) {
            $info[$key] .= ' ( ' . number_format(filesize($file) / 1024, 2) . ' KB )';
        }

        $config = [
	        'file' => '',
	        'tabs' => ['base' => '基本', 'file' => '文件', 'info' => '流程', 'notice|error' => '错误', 'sql' => 'SQL'],
	    ];

        $trace = [];
        foreach ($config['tabs'] as $name => $title) {
            $name = strtolower($name);
            switch ($name) {
                case 'base': // 基本信息
                    $trace[$title] = $base;
                    break;
                case 'file': // 文件信息
                    $trace[$title] = $info;
                    break;
                default: // 调试信息
                    if (strpos($name, '|')) {
                        // 多组信息
                        $names  = explode('|', $name);
                        $result = [];
                        foreach ($names as $item) {
                            $result = array_merge($result, $log[$item] ?? []);
                        }
                        $trace[$title] = $result;
                    } else {
                        $trace[$title] = $log[$name] ?? '';
                    }
            }
        }

        assign('trace', $trace);
        assign('runtime', $runtime);

        echo fetch('frame/pagetrace');
    }
}