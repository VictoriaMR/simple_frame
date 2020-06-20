<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminMemberService;

/**
 *  登陆控制器
 */
class LoginController extends Controller
{
	
	function __construct(AdminMemberService $service)
	{
		$this->baseService = $service;
	}

	public function login() 
	{

		print_r(Html::getCss());
		print_r(Html::getJs());
		dd();
		$phone = ipost('phone', '');
		$code = ipost('code', '');
		$password = ipost('password', '');

		if (empty($phone) || (empty($code || $password))) {
			return $this->result(10000, [], ['message' => '输入错误!']);
		}

		$result = $this->baseService->login($phone, $password, false, true);

		if ($result) {
			return $this->result(200, ['url' => url('admin/index')], ['message' => '登录成功!']);
		} else {
			return $this->result(10000, $result, ['message' => '账号不匹配!']);
		}

	}
}