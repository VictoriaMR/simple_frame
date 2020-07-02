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

	public function logout()
	{
		\frame\Session::set('admin');

		go('login.index');
	}
}