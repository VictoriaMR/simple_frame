<?php

namespace frame;

class View 
{
    private static $_instance = null;

    protected static $data = [];

    public static function getInstance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function display($template = '')
    {
        $template = $this->getTemplate($template);
        if (is_file($template)) {
            extract(self::$data);
            include($template);
        } else {
            exit($template.' was not exist!');
        }
    }

    private function getTemplate($template) 
    {
        $matchPath = '';
        $_route = \Router::$_route;
        if (empty($template)) {
            if (env('APP_VIEW_MATCH')) {
                $matchPath = (isMobile() ? 'mobile' : 'computer') . DS;
            }
            $template = 'view' . DS . implode(DS, array_map('lcfirst', $_route));
        }
        return ROOT_PATH . $matchPath . $template . '.php';
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            self::$data = array_merge(self::$data, $name);
        } else {
            self::$data[$name] = $value;
        }
        return $this;
    }

    public static function load($template = '')
    {
        return self::getInstance()->display($template);
    }
}