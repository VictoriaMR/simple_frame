<?php 

namespace App\Services;

use App\Services\MemberService as BaseService;
use App\Models\AdminMember;

/**
 * 	用户公共类
 */
class AdminMemberService extends BaseService
{	
	public function __construct(AdminMember $model)
    {
        $this->baseModel =  $model;
    }

    /**
     * @method 新增
     * @author Victoria
     * @date   2020-04-15
     * @return boolean
     */
    public function create($data)
    {
    	if (empty($data['password'])) return false;

		$data['salt'] = $this->getSalt();

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);

		$data['status'] = 1;
		$data['create_at'] = time();

		return $this->baseModel->addDataGetId($data);
    }

    public function updateData($id, $data)
    {
    	if (empty($id) || empty($data['password'])) return false;

		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_BCRYPT);
		
		$data['update_at'] = time();

		return $this->baseModel->updateDataById($id, $data);
    }
}