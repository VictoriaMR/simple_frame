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
		view();
	}
}