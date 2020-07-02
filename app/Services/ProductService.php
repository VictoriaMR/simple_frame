<?php 

namespace App\Services;

use App\Services\Base as BaseService;
use App\Services\ProductDataService;
use App\Models\Product;

/**
 * 
 */
class ProductService extends BaseService
{
	public function __construct(Product $model)
    {
        $this->baseModel = $model;
    }

    public function getList($where = [], $page = 1, $size = 10)
    {
        $total = $this->baseModel->getListTotal($where);

        if ($total > 0) {
            $list = $this->baseModel->getList($where, $page, $size);
            if (!empty($list)) {
                foreach ($list as $key => $value) {
                    $value['creat_format_at'] = date('Y-m-d H:i:s', $value['create_at']);
                    $value['pro_image'] = media($value['pro_image'], 'product');
                    $list[$k] = $value;
                }
            }
        }

        return $this->getPaginationList($list ?? [], $total, $page, $size);
    }

    public function save($proId, $data = [])
    {
        if (empty($proId) || empty($data)) return false;

        //存在更新 不存在插入
        if ($this->isExistData($proId)) {
            $this->baseModel->updateDataById($proId, $data);
        } else {
            $data['pro_id'] = $proId;
            $this->baseModel->addData($data);
        }

        return true;

    }

    /**
     * @method 是否存在数据
     * @author Victoria
     * @date   2020-04-25
     * @return boolean  
     */
    public function isExistData($proId)
    {
        return $this->baseModel->isExistData($proId);
    }
}