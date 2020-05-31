<?php

namespace App\Models;

use frame\Query;

/**
 * 封装一些常用的ORM方法，所有Model以此为基类
 */
class Base extends Query
{
    /**
     * 方法名称映射缓存
     *
     * @var array
     */
    protected $db = null;

    public function __construct()
    {
        $this->_table = $this->table;
    }

    /**
     * 通过主键获取数据
     *
     * @param mix $id 主键值
     * @return App\Model\Base
     */
    public function loadData($id)
    {
        return $this->where($this->primaryKey, (int) $id)
                    ->find();
    }

    /**
     * 通过主键更新数据
     *
     * @param mix $id
     * @param array $data
     * @return bool
     */
    public function updateDataById($id, $data)
    {
        return $this->update($this->table, [$this->primaryKey=>$id], $data);
    }

    public function addDataGetId($data)
    {
        return $this->insertGetId($data);
    }

    public function addData($data)
    {
        return $this->insert($data);
    }

    /**
     * 通过filter更新数据
     *
     * @param array $filter 更新条件 [["name", "张三"]]
     * @param array $data 更新数据
     */
    public function updateDataByFilter($filter, $data)
    {
        if (empty($filter)) return false;

        return $this->update($this->table, $filter, $data);
    }

    public function getDataCount($where = [])
    {
        $result = $this->getOne($this->table, $where, ['COUNT(*) as count']);

        return $result['count'] ?? 0;
    }

    /**
     * 获取当前业务模型列表数据
     */
    public function getDataList($where, $filed=[], $page=[], $orderBy=[])
    {
        $result = $this->getList($this->table, $where, $filed, $page, $orderBy);
        return $result;
    }

    public function getPaginationList($list, $total, $page = 1, $pagesize = 10)
    {
        return [
            'pagination' => [
                'total' => $total,
                'pagesize' => $pagesize,
                'page' => $page
            ],

            'list' => $list
        ];
    }

    /**
     * @method 是否存在数据
     * @author Victoria
     * @date   2020-04-25
     * @return boolean  
     */
    public function isExistData($primaryKeyId)
    {
        if (empty($primaryKeyId)) return false;

        $result = $this->getOne($this->table, [$this->primaryKey=>$primaryKeyId], ['COUNT(*) count']);

        return ($result['count'] ?? 0) > 0; 
    }
}