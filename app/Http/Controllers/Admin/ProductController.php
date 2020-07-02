<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use frame\Html;

/**
 * 
 */
class ProductController extends Controller
{
	
	function __construct(ProductService $service)
	{
		$this->baseService = $service;
	}

	public function list() 
	{
		$page = iGet('page', 1);
		$size = iGet('size', 30);

		$list = $this->baseService->getList([], $page, $size);

		assign(['list' => $list]);

		view();
	}
}