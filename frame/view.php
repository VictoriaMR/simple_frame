<?php

class View 
{
	/**
     * 模板变量
     * @var array
     */
    protected $data = [];

    public function display()
    {

    }

    public function fetch($template = '', $cachelate = '')
    {
    	$content = $this->display($template);

        //缓存内容
        
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

    protected function getContent($callback): string
    {
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        // 渲染输出
        try {
            $callback();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        // 获取并清空缓存
        $content = ob_get_clean();

        if ($this->filter) {
            $content = call_user_func_array($this->filter, [$content]);
        }

        return $content;
    }
}