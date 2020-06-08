<?php

namespace app\Models;

use app\Models\Base as BaseModel;

class Member extends BaseModel
{
    //表名
    public $table = 'member';

    //主键
    protected $primaryKey = 'mem_id';

    /**
	 * @method 检查用户
	 * @author Victoria
	 * @date   2020-01-09
	 * @param  string    $name 
	 * @return boolean 
	 */
    public function isExistUserByName($name)
    {
    	if (empty($name)) return false;

    	$result = $this->getOne($this->table, [[['mobile', $name], ['name', $name]]], ['COUNT(*) count']);

    	return (int) $result['count'] > 0;
    }

    /**
     * @method 检查用户
     * @author Victoria
     * @date   2020-01-09
     * @param  string    $name 
     * @return boolean 
     */
    public function isExistUserByMobile($mobile)
    {
        if (empty($mobile)) return false;

        $result = $this->getOne($this->table, ['mobile' => $mobile], ['COUNT(*) count']);

        return (int) $result['count'] > 0;
    }

    /**
     * @method 根据名称或者手机号码获取信息
     * @author Victoria
     * @date   2020-01-11
     * @return array
     */
    public function getInfoByName($name)
    {
    	$result = $this->getOne($this->table, [[['mobile', $name], ['name',$name]]]);

    	return $result;
    }

    /**
     * @method 根据手机号码获取信息
     * @author Victoria
     * @date   2020-01-11
     * @return array
     */
    public function getInfoByMobile($mobile)
    {
    	$result = $this->where('mobile', $mobile)
                       ->find();

        return $result;
    }
}