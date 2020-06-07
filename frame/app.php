<?php

use frame\Html;

class App 
{
	private static $_instance = null;
	const VERSION = '5.1.0';

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

        //注册异常处理
        Erroring::register();

        //引入公共css js
        Html::addCss(['common', 'font', 'icon', 'space']);
        Html::addJs(['jquery-3.5.1.min', 'common']);

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

		call_user_func_array([self::autoload($class), $info['Func']], []);

         // 应用调试模式
        if (getenv('APP_DEBUG')) {
            self::debugModeInit();
        }

        exit();
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
    	if (!empty($GLOBALS['autoload']) && !empty($GLOBALS['autoload'][$abstract])) {
    		$fileName = $GLOBALS['autoload'][$abstract];
    	} else {
    		$fileName = $abstract;
    	}

        $fileName = ROOT_PATH . str_replace(['\\', 'App/'], ['/', 'app/'], $fileName) . '.php';

        if (is_file($fileName)){
			require_once $fileName;
		} else {
			throw new Exception( $abstract .' was not exist!', 0);
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
    	return self::autoload(str_replace('/', '\\', $abstract));
    }

    /**
     * 调试模式设置
     * @access protected
     * @return void
     */
    protected static function debugModeInit()
    {
        // 获取基本信息
        $runtime = number_format(microtime(true) - APP_START_TIME, 10, '.', '');
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem     = number_format((memory_get_usage() - MEM0RY_START) / 1024, 2);

        $uri = implode(' ', [
        	$_SERVER['SERVER_PROTOCOL'],
        	$_SERVER['REQUEST_METHOD'],
        	$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
        ]);

        $info = get_included_files();
        $fileMem = 0;
        foreach ($info as $key => $file) {
            $temp = number_format(filesize($file) / 1024, 2);
            $fileMem += $temp;
            $info[$key] .= ' ( ' . $temp . ' KB )';
        }

        $base = [
            '请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
            '运行时间' => number_format((float) $runtime, 6) . 's [ 吞吐率：' . $reqs . ' req/s ] 内存消耗：' . $mem . ' KB 文件加载：' . count($info),
            '查询信息' => '',
            '缓存信息' => '',
            '文件总值' => $fileMem . ' KB',
        ];

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