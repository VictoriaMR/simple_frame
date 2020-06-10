<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Models\Color;

/**
 * 	背景颜色类
 */
class ColorService extends BaseService
{	
	public function __construct(Color $model)
    {
        $this->baseModel =  $model;
    }

    /**
     * @method 获取列表
     * @author Victoria
     * @date   2020-06-10
     * @return array
     */
    public function getList()
    {
    	$list = $this->baseModel->getList();
    	if (!empty($list)) {
    		$list = array_column($list, null, 'color_id');
    	}

    	return $list;
    }
}