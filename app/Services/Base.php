<?php

namespace App\Services;

/**
 * 业务模型基类.
 */
class Base
{
    /**
     * 关联数据Model.
     *
     * var App\Model\Base
     */
    protected $baseModel = null;

    /**
     * 常量映射关系表.
     */
    protected static $constantMap = [];

    /**
     * 通过主键获取资料.
     *
     * @param mix $id 主键值
     *
     * @return array
     */
    public function loadData($id)
    {
        return $this->baseModel->loadData($id);
    }

    /**
     * 新增数据.
     *
     * @param array $data 新增数据
     */
    public function addData($data)
    {
        return $this->baseModel->insert($data);
    }

    /**
     * 新增数据.
     *
     * @param array $data 新增数据
     */
    public function addDataGetId($data)
    {
        return $this->baseModel->insert($data);
    }

    /**
     * 通过主键更新数据.
     *
     * @param mix   $id
     * @param array $data
     *
     * @return bool
     */
    public function updateData($id, $data)
    {
        return $this->baseModel->updateDataById($id, $data);
    }

    /**
     * 通过属性获取资料.
     *
     * @param string $attribute 属性名
     * @param mix    $value     属性值
     *
     * @return array
     */
    public function loadDataByAttribute($attribute, $value)
    {
        return $this->baseModel->loadDataByAttribute($attribute, $value); 
    }

    /**
     * @method 获取条件总数
     * @author Victoria
     * @date   2020-05-31
     * @return integer
     */
    public function getDataCount($where = [])
    {
        return $this->baseModel->getDataCount($where);
    }

    /**
     * @method 获取数据
     * @author Victoria
     * @date   2020-04-13
     * @return array
     */
    public function getDataList($data = [], $field=[], $page=[], $orderBy=[])
    {
        return $this->baseModel->getDataList($data, $field, $page, $orderBy);
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

    /**
     * 通过属性值更新数据.
     *
     * @param mix   $id
     * @param array $data
     *
     * @return bool
     */
    public function updateDataByAttribute($attribute, $value, $data)
    {
        return $this->baseModel->updateDataByAttribute($attribute, $value, $data);
    }

    /**
     * 通过filter更新数据.
     *
     * @param array $filter 更新条件 
     * @param array $data   更新数据
     */
    public function updateDataByFilter($filter, $data)
    {
        return $this->baseModel->updateDataByFilter($filter, $data);
    }

    /**
     * 通过主键进行删除.
     *
     * @param $id 主键值
     */
    public function deleteData($id)
    {
        return $this->baseModel->deleteData($id);
    }

    /**
     *  获取Model.
     */
    public function getBaseModel()
    {
        return $this->baseModel;
    }

    /**
     * 获取常量继承方法
     * @author   Mingrong
     * @DateTime 2020-01-10
     * @param    [type]     $const [description]
     * @param    string     $model [description]
     * @return   
     */
    public static function constant($const, $model = 'base')
    {
        $namespace = 'static';

        if (isset(static::$constantMap[$model])) {
            $namespace = static::$constantMap[$model];
        }

        return constant($namespace.'::'.$const);
    }

    /**
     * @method 返回页码总数格式
     * @author Victoria
     * @date   2020-04-13
     * @return array
     */
    public function getPaginationList($list, $total, $page = 1, $pagesize = 10)
    {
        return $this->baseModel->getPaginationList($list, $total, $page, $pagesize);
    }
}