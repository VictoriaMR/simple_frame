<?php

namespace App\Models;

use App\Models\Base as BaseModel;

/**
 * 	背景颜色类
 */
class Color extends BaseModel
{
	//表名
    public $table = 'color';

    //主键
    protected $primaryKey = 'color_id';

    /**
     * @method 获取列表
     * @author Victoria
     * @date   2020-06-10
     * @return array
     */
    public function getList()
    {
    	return $this->where('status', 1)->get();
    }
}