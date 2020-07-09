<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use frame\Html;
use frame\Session;

/**
 * 
 */
class IndexController extends Controller
{
	
	function __construct()
	{
		# code...
	}

	public function index() 
	{
		Html::addCss('admin/common');
		Html::addJs('admin/index');

		//获取喜好颜色
		$colorService = \App::make('App\Services\Admin\ColorService');
		$colorList = $colorService->getList();

		//获取控制器列表
		$controllerService = \App::make('App\Services\Admin\ControllerService');

		$controllerList = $controllerService->getListFormat();

		$info = Session::getInfo('admin');

		assign('info', $info);
		assign('list', $controllerList);

		view();
	}

	public function show() 
	{
		view();
	}
}