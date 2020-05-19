<?php

/**
 *  服务容器类
 */
final class Container
{
    static private $instance;

    /**
     *  容器绑定，用来装提供的实例或者 提供实例的回调函数
     * @var array
     */
    static protected $building = [];

    public function __construct(){}

    private function __clone(){}

    static public function getInstance()
    {
        //判断$instance是否是Singleton的对象，不是则创建
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function autoload($abstract) 
    {
        $concrete = self::make($abstract);

        return $concrete;
    }

    /**
     * 注册一个绑定到容器
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)){
            $concrete = $abstract;
        }

        if (!$concrete instanceOf Closure){
            $concrete = $this->getClosure($abstract, $concrete);
            print_r($concrete);
        }

        self::$building[$abstract] =  compact('concrete', 'shared');
    }

    //注册一个共享的绑定 单例
    public function singleton($abstract, $concrete, $shared = true)
    {
        $this->bind($abstract, $concrete, $shared);
    }

    /**
     * 默认生成实例的回调闭包
     *
     * @param $abstract
     * @param $concrete
     * @return Closure
     */
    public function getClosure($abstract, $concrete)
    {
        return function($c) use($abstract, $concrete){
            $method = ($abstract == $concrete)? 'build' : 'make';

            return $c->$method($concrete);
        };
    }

    /**
     * 生成实例 
     */
    public function make($abstract)
    {
        if (!empty(self::$building[$abstract]))
            return self::$building[$abstract];
             
        $concrete = $this->getConcrete($abstract);

        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        return $object;
    }

    /**
     * 获取绑定的回调函数
     */
    public function getConcrete($abstract)
    {
        if (empty(self::$building[$abstract])){
            return $abstract;
        }

        return self::$building[$abstract];
    }

    /**
     * 判断 是否 可以创建服务实体
     */
    public function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * 根据实例具体名称实例具体对象
     */
    public function build($concrete)
    {
        if($concrete instanceof Closure){
            return $concrete($this);
        }

        //创建反射对象
        $reflector = new ReflectionClass($concrete);

        if( ! $reflector->isInstantiable()){
            //抛出异常
            throw new \Exception('无法实例化');
        }

        $constructor = $reflector->getConstructor();
        if(is_null($constructor)){
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instance = $this->getDependencies($dependencies);

        $object = $reflector->newInstanceArgs($instance);

        self::$building[$concrete] = $object;

        return $object;

    }

    //通过反射解决参数依赖
    public function getDependencies(array $dependencies)
    {
        $results = [];
        foreach( $dependencies as $dependency ){
            $results[] = is_null($dependency->getClass())
                ?$this->resolvedNonClass($dependency)
                :$this->resolvedClass($dependency);
        }

        return $results;
    }

    //解决一个没有类型提示依赖
    public function resolvedNonClass(ReflectionParameter $parameter)
    {
        if($parameter->isDefaultValueAvailable()){
            return $parameter->getDefaultValue();
        }
        throw new \Exception('出错');

    }

    //通过容器解决依赖
    public function resolvedClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);

    }
}