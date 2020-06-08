<?php

namespace App\Http\Controllers;

class Controller 
{
	/**
     * 封装接口返回数据格式
     *
     * @param int $code 错误代码
     * @param mix $data 返回数据
     * @param array $options 扩展参数
     * @return array
     */
    protected function result($code, $data=[], $options=[])
    {
        $data = [
            'code' => $code,
            'message' => isset($options['message']) ? $options['message'] : '',
            'data' => $data
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
