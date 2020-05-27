<?php

class View 
{
    private static $_instance = null;

	/**
     * 模板变量
     * @var array
     */
    protected $data = [];

    public static function getInstance() 
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function display($template = '')
    {
        if (empty($template))
            $template = 'view/'.implode('/', Router::analyze_func());

        $tplFile = ROOT_PATH . $template . '.php';

        if (!file_exists($tplFile)) {
            throw new Exception($tplFile . ' 模板不存在', 1);
        }

        extract($this->data);

        include($tplFile);
    }

    public function fetch($template = '', $cachelate = '')
    {
        ob_start();

        $this->display($template);

        $content = ob_get_clean();

        if (!empty($cachelate)) {

        }

        return $content;
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }

        return $this;
    }
}