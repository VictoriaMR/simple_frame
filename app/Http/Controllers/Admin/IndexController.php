<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use frame\Html;

/**
 * 
 */
class IndexController extends Controller
{
	
	function __construct()
	{
		# code...
	}

	

	public function login()
	{
		Html::addCss('admin/login');

		$attachmentService = \App::make('App/Services/AttachmentService');

		$data = $attachmentService->getListByTypeOne($attachmentService::constant('TYPE_ADMIN_LOGIN_BACKGROUD'));
		assign(['bg_img' => $data['url']]);
		view();
	}
}