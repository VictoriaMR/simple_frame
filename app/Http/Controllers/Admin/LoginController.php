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

		$fileService = \App::make('App/Services/FileService');

		$data = [
			'name' => 'E:/Catch/litfad/202006/sync3/10000/201856.jpg', 
			'tmp_name' =>'E:/Catch/litfad/202006/sync3/10000/201856.jpg'
		];
		$fileService->upload($data, 'product', '10000');

		dd(12312);

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