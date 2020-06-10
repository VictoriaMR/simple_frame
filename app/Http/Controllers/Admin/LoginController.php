<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminMemberService;
use frame\Html;

/**
 *  登陆控制器
 */
class LoginController extends Controller
{
	
	function __construct(AdminMemberService $service)
	{
		$this->baseService = $service;
	}

	public function index()
	{
		Html::addCss('admin/login');
		Html::addJs('admin/login');

		$attachmentService = \App::make('App/Services/AttachmentService');

		$data = $attachmentService->getListByTypeOne($attachmentService::constant('TYPE_ADMIN_LOGIN_BACKGROUD'));
		
		assign(['bg_img' => $data['url']]);
		
		view();
	}

	public function login() 
	{
		$phone = ipost('phone', '');
		$code = ipost('code', '');
		$password = ipost('password', '');
		$isAjax = ipost('is_ajax', 0);

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

	public function logout()
	{
		\frame\Session::set('admin');

		go('login.index');
	}
}