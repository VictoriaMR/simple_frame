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

		$colorService = \App::make('App\Services\ColorService');
		// $colorList = $colorService->getList();

		$info = Session::getInfo('admin');

		assign('info', $info);

		view();
	}

	public function show() 
	{
		view();
	}
}